package cmd

import (
	gio "github.com/melkmeshi/laravel-go-tools/internal/io"

	h3pkg "github.com/melkmeshi/laravel-go-tools/internal/h3"
	"github.com/spf13/cobra"
)

var h3IndexCmd = &cobra.Command{
	Use:   "index",
	Short: "Convert lat/lng to H3 cell index",
	RunE: func(cmd *cobra.Command, args []string) error {
		var input struct {
			Lat        float64 `json:"lat"`
			Lng        float64 `json:"lng"`
			Resolution int     `json:"resolution"`
		}
		if err := gio.ReadInput(&input); err != nil {
			return gio.WriteError(err.Error())
		}

		cell, err := h3pkg.LatLngToCell(input.Lat, input.Lng, input.Resolution)
		if err != nil {
			return gio.WriteError(err.Error())
		}

		return gio.WriteOutput(map[string]any{"index": cell})
	},
}

func init() {
	h3Cmd.AddCommand(h3IndexCmd)
}
