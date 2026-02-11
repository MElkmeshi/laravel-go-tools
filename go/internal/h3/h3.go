package h3

import (
	"fmt"

	"github.com/uber/h3-go/v4"
)

// parseCell converts a hex string to an H3 Cell.
func parseCell(s string) (h3.Cell, error) {
	var c h3.Cell
	if err := c.UnmarshalText([]byte(s)); err != nil {
		return c, fmt.Errorf("invalid H3 cell index %q: %w", s, err)
	}
	if !c.IsValid() {
		return c, fmt.Errorf("invalid H3 cell index %q", s)
	}
	return c, nil
}

// LatLngToCell converts a lat/lng pair to an H3 cell index at the given resolution.
func LatLngToCell(lat, lng float64, resolution int) (string, error) {
	if resolution < 0 || resolution > 15 {
		return "", fmt.Errorf("resolution must be between 0 and 15, got %d", resolution)
	}

	latLng := h3.NewLatLng(lat, lng)
	cell := h3.LatLngToCell(latLng, resolution)

	return cell.String(), nil
}

// KRing returns the k-ring of cells around the given cell index.
func KRing(cellIndex string, k int) ([]string, error) {
	cell, err := parseCell(cellIndex)
	if err != nil {
		return nil, err
	}

	disk := cell.GridDisk(k)
	result := make([]string, len(disk))
	for i, c := range disk {
		result[i] = c.String()
	}
	return result, nil
}

// GridDistance returns the grid distance between two H3 cells.
func GridDistance(originIndex, destIndex string) (int, error) {
	origin, err := parseCell(originIndex)
	if err != nil {
		return 0, err
	}

	dest, err := parseCell(destIndex)
	if err != nil {
		return 0, err
	}

	dist := origin.GridDistance(dest)
	return dist, nil
}

// PolygonToCells fills a polygon with H3 cells at the given resolution.
func PolygonToCells(polygon [][]float64, resolution int) ([]string, error) {
	if resolution < 0 || resolution > 15 {
		return nil, fmt.Errorf("resolution must be between 0 and 15, got %d", resolution)
	}
	if len(polygon) < 3 {
		return nil, fmt.Errorf("polygon must have at least 3 vertices")
	}

	loop := make([]h3.LatLng, len(polygon))
	for i, coord := range polygon {
		if len(coord) != 2 {
			return nil, fmt.Errorf("each coordinate must have exactly 2 elements [lat, lng], got %d at index %d", len(coord), i)
		}
		loop[i] = h3.NewLatLng(coord[0], coord[1])
	}

	geoLoop := h3.GeoLoop(loop)
	poly := h3.GeoPolygon{GeoLoop: geoLoop}

	cells := poly.Cells(resolution)
	result := make([]string, len(cells))
	for i, c := range cells {
		result[i] = c.String()
	}
	return result, nil
}
