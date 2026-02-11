package osrm

import (
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"strings"
	"time"
)

type Client struct {
	BaseURL    string
	HTTPClient *http.Client
}

func NewClient(baseURL string) *Client {
	return &Client{
		BaseURL: strings.TrimRight(baseURL, "/"),
		HTTPClient: &http.Client{
			Timeout: 30 * time.Second,
		},
	}
}

// RouteResponse represents the OSRM route API response.
type RouteResponse struct {
	Code      string  `json:"code"`
	Routes    []Route `json:"routes"`
	Waypoints []struct {
		Name     string    `json:"name"`
		Location []float64 `json:"location"`
	} `json:"waypoints"`
	Message string `json:"message,omitempty"`
}

type Route struct {
	Distance float64 `json:"distance"`
	Duration float64 `json:"duration"`
	Geometry string  `json:"geometry"`
}

// TableResponse represents the OSRM table API response.
type TableResponse struct {
	Code         string      `json:"code"`
	Durations    [][]float64 `json:"durations"`
	Distances    [][]float64 `json:"distances"`
	Sources      []Location  `json:"sources"`
	Destinations []Location  `json:"destinations"`
	Message      string      `json:"message,omitempty"`
}

type Location struct {
	Name     string    `json:"name"`
	Location []float64 `json:"location"`
}

// GetRoute calls the OSRM route API.
func (c *Client) GetRoute(coordinates [][]float64) (*RouteResponse, error) {
	coords := formatCoordinates(coordinates)
	url := fmt.Sprintf("%s/route/v1/driving/%s?overview=full&geometries=polyline", c.BaseURL, coords)

	resp, err := c.HTTPClient.Get(url)
	if err != nil {
		return nil, fmt.Errorf("OSRM route request failed: %w", err)
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("failed to read OSRM response: %w", err)
	}

	var result RouteResponse
	if err := json.Unmarshal(body, &result); err != nil {
		return nil, fmt.Errorf("failed to parse OSRM response: %w", err)
	}

	if result.Code != "Ok" {
		return nil, fmt.Errorf("OSRM error: %s - %s", result.Code, result.Message)
	}

	return &result, nil
}

// GetTable calls the OSRM table API.
func (c *Client) GetTable(origins, destinations [][]float64) (*TableResponse, error) {
	allCoords := append(origins, destinations...)
	coords := formatCoordinates(allCoords)

	// Build source/destination indices
	sourceIndices := make([]string, len(origins))
	for i := range origins {
		sourceIndices[i] = fmt.Sprintf("%d", i)
	}
	destIndices := make([]string, len(destinations))
	for i := range destinations {
		destIndices[i] = fmt.Sprintf("%d", len(origins)+i)
	}

	url := fmt.Sprintf(
		"%s/table/v1/driving/%s?sources=%s&destinations=%s&annotations=duration,distance",
		c.BaseURL,
		coords,
		strings.Join(sourceIndices, ";"),
		strings.Join(destIndices, ";"),
	)

	resp, err := c.HTTPClient.Get(url)
	if err != nil {
		return nil, fmt.Errorf("OSRM table request failed: %w", err)
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("failed to read OSRM response: %w", err)
	}

	var result TableResponse
	if err := json.Unmarshal(body, &result); err != nil {
		return nil, fmt.Errorf("failed to parse OSRM response: %w", err)
	}

	if result.Code != "Ok" {
		return nil, fmt.Errorf("OSRM error: %s - %s", result.Code, result.Message)
	}

	return &result, nil
}

// formatCoordinates formats coordinate pairs as "lng,lat;lng,lat;..."
// Note: OSRM uses lng,lat order (opposite of H3 which uses lat,lng).
func formatCoordinates(coordinates [][]float64) string {
	parts := make([]string, len(coordinates))
	for i, coord := range coordinates {
		// Input is [lat, lng], OSRM expects lng,lat
		parts[i] = fmt.Sprintf("%f,%f", coord[1], coord[0])
	}
	return strings.Join(parts, ";")
}
