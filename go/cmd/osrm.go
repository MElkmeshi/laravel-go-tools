package cmd

import (
	"github.com/spf13/cobra"
)

var osrmCmd = &cobra.Command{
	Use:   "osrm",
	Short: "OSRM routing operations",
}

func init() {
	rootCmd.AddCommand(osrmCmd)
}
