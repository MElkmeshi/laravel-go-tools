package cmd

import (
	"github.com/spf13/cobra"
)

var rootCmd = &cobra.Command{
	Use:   "go-tools",
	Short: "Go-powered tools for Laravel",
	Long:  "High-performance CLI tools for Laravel: H3 geospatial, OSRM routing, and fast set operations.",
}

func Execute() error {
	return rootCmd.Execute()
}
