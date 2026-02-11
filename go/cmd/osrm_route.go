package cmd

import (
	gio "github.com/melkmeshi/laravel-go-tools/internal/io"
	"github.com/melkmeshi/laravel-go-tools/internal/osrm"
	"github.com/spf13/cobra"
)

var osrmRouteCmd = &cobra.Command{
	Use:   "route",
	Short: "Calculate a route between coordinates",
	RunE: func(cmd *cobra.Command, args []string) error {
		var input struct {
			Coordinates [][]float64 `json:"coordinates"`
			ServerURL   string      `json:"server_url"`
		}
		if err := gio.ReadInput(&input); err != nil {
			return gio.WriteError(err.Error())
		}

		if len(input.Coordinates) < 2 {
			return gio.WriteError("at least 2 coordinates are required")
		}

		if input.ServerURL == "" {
			input.ServerURL = "http://router.project-osrm.org"
		}

		client := osrm.NewClient(input.ServerURL)
		result, err := client.GetRoute(input.Coordinates)
		if err != nil {
			return gio.WriteError(err.Error())
		}

		return gio.WriteOutput(result)
	},
}

func init() {
	osrmCmd.AddCommand(osrmRouteCmd)
}
