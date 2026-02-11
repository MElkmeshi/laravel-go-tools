package cmd

import (
	gio "github.com/melkmeshi/laravel-go-tools/internal/io"

	h3pkg "github.com/melkmeshi/laravel-go-tools/internal/h3"
	"github.com/spf13/cobra"
)

var h3KringCmd = &cobra.Command{
	Use:   "kring",
	Short: "Get k-ring of cells around a given cell",
	RunE: func(cmd *cobra.Command, args []string) error {
		var input struct {
			Index string `json:"index"`
			K     int    `json:"k"`
		}
		if err := gio.ReadInput(&input); err != nil {
			return gio.WriteError(err.Error())
		}

		cells, err := h3pkg.KRing(input.Index, input.K)
		if err != nil {
			return gio.WriteError(err.Error())
		}

		return gio.WriteOutput(map[string]any{"cells": cells})
	},
}

func init() {
	h3Cmd.AddCommand(h3KringCmd)
}
