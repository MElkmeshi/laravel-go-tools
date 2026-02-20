package cmd

import (
	"github.com/melkmeshi/laravel-go-tools/internal/db"
	gio "github.com/melkmeshi/laravel-go-tools/internal/io"
	"github.com/spf13/cobra"
)

var dbCompareCmd = &cobra.Command{
	Use:   "compare",
	Short: "Compare IDs between two database sources",
	RunE: func(cmd *cobra.Command, args []string) error {
		var input db.CompareInput
		if err := gio.ReadInput(&input); err != nil {
			return gio.WriteError(err.Error())
		}

		result, err := db.Compare(input)
		if err != nil {
			return gio.WriteError(err.Error())
		}

		return gio.WriteOutput(result)
	},
}

func init() {
	dbCmd.AddCommand(dbCompareCmd)
}
