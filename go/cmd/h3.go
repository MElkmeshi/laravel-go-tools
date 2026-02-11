package cmd

import (
	"github.com/spf13/cobra"
)

var h3Cmd = &cobra.Command{
	Use:   "h3",
	Short: "H3 geospatial indexing operations",
}

func init() {
	rootCmd.AddCommand(h3Cmd)
}
