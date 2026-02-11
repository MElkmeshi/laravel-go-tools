package main

import (
	"os"

	"github.com/melkmeshi/laravel-go-tools/cmd"
)

func main() {
	if err := cmd.Execute(); err != nil {
		os.Exit(1)
	}
}
