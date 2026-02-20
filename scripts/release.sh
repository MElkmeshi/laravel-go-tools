#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
BUILD_DIR="$PROJECT_DIR/dist"

VERSION="${1:-}"

if [[ -z "$VERSION" ]]; then
    echo "Usage: ./scripts/release.sh <version>"
    echo "Example: ./scripts/release.sh v0.2.0"
    exit 1
fi

if ! command -v gh &>/dev/null; then
    echo "ERROR: GitHub CLI (gh) is required. Install with: brew install gh"
    exit 1
fi

# Build all binaries
echo "==> Building binaries..."
bash "$SCRIPT_DIR/build.sh"
echo ""

# Check for existing release
if gh release view "$VERSION" &>/dev/null; then
    echo "Release $VERSION already exists. Deleting..."
    gh release delete "$VERSION" --yes
    git push origin ":refs/tags/$VERSION" 2>/dev/null || true
fi

# Create the release
echo "==> Creating GitHub release $VERSION..."
gh release create "$VERSION" \
    "$BUILD_DIR"/go-tools-*.tar.gz \
    --target main \
    --title "$VERSION" \
    --generate-notes

echo ""
echo "Release created: https://github.com/$(gh repo view --json nameWithOwner -q .nameWithOwner)/releases/tag/$VERSION"
