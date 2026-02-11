package cmd

import (
	gio "github.com/melkmeshi/laravel-go-tools/internal/io"

	h3pkg "github.com/melkmeshi/laravel-go-tools/internal/h3"
	"github.com/spf13/cobra"
)

var h3PolyfillCmd = &cobra.Command{
	Use:   "polyfill",
	Short: "Fill a polygon with H3 cells",
	RunE: func(cmd *cobra.Command, args []string) error {
		var input struct {
			Polygon    [][]float64 `json:"polygon"`
			Resolution int         `json:"resolution"`
		}
		if err := gio.ReadInput(&input); err != nil {
			return gio.WriteError(err.Error())
		}

		cells, err := h3pkg.PolygonToCells(input.Polygon, input.Resolution)
		if err != nil {
			return gio.WriteError(err.Error())
		}

		return gio.WriteOutput(map[string]any{"cells": cells})
	},
}

func init() {
	h3Cmd.AddCommand(h3PolyfillCmd)
}
