package cmd

import (
	"github.com/spf13/cobra"
)

var setsCmd = &cobra.Command{
	Use:   "sets",
	Short: "Fast set operations on large collections",
}

func init() {
	rootCmd.AddCommand(setsCmd)
}
