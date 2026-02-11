package cmd

import (
	gio "github.com/melkmeshi/laravel-go-tools/internal/io"

	h3pkg "github.com/melkmeshi/laravel-go-tools/internal/h3"
	"github.com/spf13/cobra"
)

var h3DistanceCmd = &cobra.Command{
	Use:   "distance",
	Short: "Calculate grid distance between two H3 cells",
	RunE: func(cmd *cobra.Command, args []string) error {
		var input struct {
			Origin      string `json:"origin"`
			Destination string `json:"destination"`
		}
		if err := gio.ReadInput(&input); err != nil {
			return gio.WriteError(err.Error())
		}

		dist, err := h3pkg.GridDistance(input.Origin, input.Destination)
		if err != nil {
			return gio.WriteError(err.Error())
		}

		return gio.WriteOutput(map[string]any{"distance": dist})
	},
}

func init() {
	h3Cmd.AddCommand(h3DistanceCmd)
}
