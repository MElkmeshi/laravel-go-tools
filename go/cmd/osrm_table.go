package cmd

import (
	gio "github.com/melkmeshi/laravel-go-tools/internal/io"
	"github.com/melkmeshi/laravel-go-tools/internal/osrm"
	"github.com/spf13/cobra"
)

var osrmTableCmd = &cobra.Command{
	Use:   "table",
	Short: "Calculate distance/duration matrix between coordinates",
	RunE: func(cmd *cobra.Command, args []string) error {
		var input struct {
			Origins      [][]float64 `json:"origins"`
			Destinations [][]float64 `json:"destinations"`
			ServerURL    string      `json:"server_url"`
		}
		if err := gio.ReadInput(&input); err != nil {
			return gio.WriteError(err.Error())
		}

		if len(input.Origins) == 0 || len(input.Destinations) == 0 {
			return gio.WriteError("origins and destinations are required")
		}

		if input.ServerURL == "" {
			input.ServerURL = "http://router.project-osrm.org"
		}

		client := osrm.NewClient(input.ServerURL)
		result, err := client.GetTable(input.Origins, input.Destinations)
		if err != nil {
			return gio.WriteError(err.Error())
		}

		return gio.WriteOutput(result)
	},
}

func init() {
	osrmCmd.AddCommand(osrmTableCmd)
}
