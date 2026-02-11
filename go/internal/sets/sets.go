package sets

// Intersect returns elements present in both a and b.
func Intersect(a, b []any) []any {
	set := make(map[any]struct{}, len(b))
	for _, v := range b {
		set[v] = struct{}{}
	}

	var result []any
	for _, v := range a {
		if _, ok := set[v]; ok {
			result = append(result, v)
		}
	}
	return result
}

// Union returns all unique elements from a and b.
func Union(a, b []any) []any {
	set := make(map[any]struct{}, len(a)+len(b))
	var result []any

	for _, v := range a {
		if _, ok := set[v]; !ok {
			set[v] = struct{}{}
			result = append(result, v)
		}
	}
	for _, v := range b {
		if _, ok := set[v]; !ok {
			set[v] = struct{}{}
			result = append(result, v)
		}
	}
	return result
}

// Diff returns elements in a that are not in b.
func Diff(a, b []any) []any {
	set := make(map[any]struct{}, len(b))
	for _, v := range b {
		set[v] = struct{}{}
	}

	var result []any
	for _, v := range a {
		if _, ok := set[v]; !ok {
			result = append(result, v)
		}
	}
	return result
}
