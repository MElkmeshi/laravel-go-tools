package db

import (
	"database/sql"
	"encoding/csv"
	"fmt"
	"os"
	"path/filepath"
	"sync"

	_ "github.com/go-sql-driver/mysql"
	_ "github.com/lib/pq"
)

type Source struct {
	Driver string `json:"driver"`
	DSN    string `json:"dsn"`
	Query  string `json:"query"`
}

type CompareInput struct {
	SourceA   Source `json:"source_a"`
	SourceB   Source `json:"source_b"`
	OutputDir string `json:"output_dir"`
}

type CompareResult struct {
	CountA     int    `json:"count_a"`
	CountB     int    `json:"count_b"`
	OnlyACount int    `json:"only_a_count"`
	OnlyBCount int    `json:"only_b_count"`
	BothCount  int    `json:"both_count"`
	OnlyAFile  string `json:"only_a_file"`
	OnlyBFile  string `json:"only_b_file"`
	BothFile   string `json:"both_file"`
}

var validDrivers = map[string]bool{
	"mysql":    true,
	"postgres": true,
}

func Compare(input CompareInput) (*CompareResult, error) {
	if err := validateInput(input); err != nil {
		return nil, err
	}

	if err := os.MkdirAll(input.OutputDir, 0755); err != nil {
		return nil, fmt.Errorf("failed to create output directory: %w", err)
	}

	var (
		setA, setB map[string]struct{}
		errA, errB error
		wg         sync.WaitGroup
	)

	wg.Add(2)

	go func() {
		defer wg.Done()
		setA, errA = queryToSet(input.SourceA)
	}()

	go func() {
		defer wg.Done()
		setB, errB = queryToSet(input.SourceB)
	}()

	wg.Wait()

	if errA != nil {
		return nil, fmt.Errorf("source_a query failed: %w", errA)
	}
	if errB != nil {
		return nil, fmt.Errorf("source_b query failed: %w", errB)
	}

	onlyA, onlyB, both := diffSets(setA, setB)

	onlyAFile := filepath.Join(input.OutputDir, "only_a.csv")
	onlyBFile := filepath.Join(input.OutputDir, "only_b.csv")
	bothFile := filepath.Join(input.OutputDir, "both.csv")

	if err := writeCSV(onlyAFile, onlyA); err != nil {
		return nil, fmt.Errorf("failed to write only_a.csv: %w", err)
	}
	if err := writeCSV(onlyBFile, onlyB); err != nil {
		return nil, fmt.Errorf("failed to write only_b.csv: %w", err)
	}
	if err := writeCSV(bothFile, both); err != nil {
		return nil, fmt.Errorf("failed to write both.csv: %w", err)
	}

	return &CompareResult{
		CountA:     len(setA),
		CountB:     len(setB),
		OnlyACount: len(onlyA),
		OnlyBCount: len(onlyB),
		BothCount:  len(both),
		OnlyAFile:  onlyAFile,
		OnlyBFile:  onlyBFile,
		BothFile:   bothFile,
	}, nil
}

func validateInput(input CompareInput) error {
	if !validDrivers[input.SourceA.Driver] {
		return fmt.Errorf("unsupported driver for source_a: %q (supported: mysql, postgres)", input.SourceA.Driver)
	}
	if !validDrivers[input.SourceB.Driver] {
		return fmt.Errorf("unsupported driver for source_b: %q (supported: mysql, postgres)", input.SourceB.Driver)
	}
	if input.SourceA.DSN == "" {
		return fmt.Errorf("source_a.dsn is required")
	}
	if input.SourceB.DSN == "" {
		return fmt.Errorf("source_b.dsn is required")
	}
	if input.SourceA.Query == "" {
		return fmt.Errorf("source_a.query is required")
	}
	if input.SourceB.Query == "" {
		return fmt.Errorf("source_b.query is required")
	}
	if input.OutputDir == "" {
		return fmt.Errorf("output_dir is required")
	}
	return nil
}

func queryToSet(src Source) (map[string]struct{}, error) {
	conn, err := sql.Open(src.Driver, src.DSN)
	if err != nil {
		return nil, fmt.Errorf("failed to open %s connection: %w", src.Driver, err)
	}
	defer conn.Close()

	if err := conn.Ping(); err != nil {
		return nil, fmt.Errorf("failed to ping %s: %w", src.Driver, err)
	}

	rows, err := conn.Query(src.Query)
	if err != nil {
		return nil, fmt.Errorf("query execution failed: %w", err)
	}
	defer rows.Close()

	cols, err := rows.Columns()
	if err != nil {
		return nil, fmt.Errorf("failed to get columns: %w", err)
	}
	if len(cols) != 1 {
		return nil, fmt.Errorf("query must return exactly 1 column, got %d", len(cols))
	}

	set := make(map[string]struct{})
	for rows.Next() {
		var val interface{}
		if err := rows.Scan(&val); err != nil {
			return nil, fmt.Errorf("failed to scan row: %w", err)
		}
		set[normalizeValue(val)] = struct{}{}
	}

	if err := rows.Err(); err != nil {
		return nil, fmt.Errorf("row iteration error: %w", err)
	}

	return set, nil
}

func normalizeValue(v interface{}) string {
	switch val := v.(type) {
	case []byte:
		return string(val)
	case int64:
		return fmt.Sprintf("%d", val)
	case float64:
		return fmt.Sprintf("%g", val)
	case string:
		return val
	case nil:
		return ""
	default:
		return fmt.Sprintf("%v", val)
	}
}

func diffSets(a, b map[string]struct{}) (onlyA, onlyB, both []string) {
	for k := range a {
		if _, ok := b[k]; ok {
			both = append(both, k)
		} else {
			onlyA = append(onlyA, k)
		}
	}
	for k := range b {
		if _, ok := a[k]; !ok {
			onlyB = append(onlyB, k)
		}
	}
	return
}

func writeCSV(path string, ids []string) error {
	f, err := os.Create(path)
	if err != nil {
		return err
	}
	defer f.Close()

	w := csv.NewWriter(f)
	defer w.Flush()

	if err := w.Write([]string{"id"}); err != nil {
		return err
	}
	for _, id := range ids {
		if err := w.Write([]string{id}); err != nil {
			return err
		}
	}
	return nil
}
