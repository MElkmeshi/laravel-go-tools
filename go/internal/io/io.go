package io

import (
	"encoding/json"
	"fmt"
	"io"
	"os"
)

// ReadInput reads JSON from stdin and decodes into the given target.
func ReadInput(target any) error {
	data, err := io.ReadAll(os.Stdin)
	if err != nil {
		return fmt.Errorf("failed to read stdin: %w", err)
	}
	if len(data) == 0 {
		return fmt.Errorf("no input provided on stdin")
	}
	if err := json.Unmarshal(data, target); err != nil {
		return fmt.Errorf("invalid JSON input: %w", err)
	}
	return nil
}

// WriteOutput encodes the given value as JSON and writes to stdout.
func WriteOutput(v any) error {
	data, err := json.Marshal(v)
	if err != nil {
		return fmt.Errorf("failed to encode output: %w", err)
	}
	_, err = fmt.Fprintln(os.Stdout, string(data))
	return err
}

// WriteError writes an error message to stderr and returns a non-nil error
// to signal a non-zero exit code.
func WriteError(msg string) error {
	fmt.Fprintln(os.Stderr, msg)
	return fmt.Errorf(msg)
}
