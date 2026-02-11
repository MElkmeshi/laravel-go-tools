package cmd

import (
	gio "github.com/melkmeshi/laravel-go-tools/internal/io"
	"github.com/melkmeshi/laravel-go-tools/internal/sets"
	"github.com/spf13/cobra"
)

var setsDiffCmd = &cobra.Command{
	Use:   "diff",
	Short: "Return elements in set_a that are not in set_b",
	RunE: func(cmd *cobra.Command, args []string) error {
		var input struct {
			SetA []any `json:"set_a"`
			SetB []any `json:"set_b"`
		}
		if err := gio.ReadInput(&input); err != nil {
			return gio.WriteError(err.Error())
		}

		result := sets.Diff(input.SetA, input.SetB)
		return gio.WriteOutput(map[string]any{"result": result})
	},
}

func init() {
	setsCmd.AddCommand(setsDiffCmd)
}
