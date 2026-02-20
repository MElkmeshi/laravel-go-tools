#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
GO_DIR="$PROJECT_DIR/go"
BUILD_DIR="$PROJECT_DIR/dist"

PLATFORMS=(
    "darwin-arm64"
    "linux-amd64"
)

rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR"

echo "Building Go binaries..."
echo ""

for PLATFORM in "${PLATFORMS[@]}"; do
    OS="${PLATFORM%-*}"
    ARCH="${PLATFORM#*-}"

    echo "  Building $OS/$ARCH..."

    export CGO_ENABLED=1
    export GOOS="$OS"
    export GOARCH="$ARCH"

    # Use zig as cross-compiler for non-native platforms
    NATIVE_OS="$(uname -s | tr '[:upper:]' '[:lower:]')"
    NATIVE_ARCH="$(uname -m)"
    [[ "$NATIVE_ARCH" == "x86_64" ]] && NATIVE_ARCH="amd64"
    [[ "$NATIVE_ARCH" == "aarch64" ]] && NATIVE_ARCH="arm64"

    if [[ "$OS-$ARCH" != "$NATIVE_OS-$NATIVE_ARCH" ]]; then
        if ! command -v zig &>/dev/null; then
            echo "    ERROR: zig is required for cross-compilation. Install with: brew install zig"
            exit 1
        fi

        ZIG_TARGET=""
        case "$OS-$ARCH" in
            linux-amd64)  ZIG_TARGET="x86_64-linux-gnu" ;;
            linux-arm64)  ZIG_TARGET="aarch64-linux-gnu" ;;
            darwin-amd64) ZIG_TARGET="x86_64-macos" ;;
            darwin-arm64) ZIG_TARGET="aarch64-macos" ;;
        esac

        export CC="zig cc -target $ZIG_TARGET"
        export CXX="zig c++ -target $ZIG_TARGET"
    else
        unset CC CXX 2>/dev/null || true
    fi

    OUTDIR="$BUILD_DIR/$PLATFORM"
    mkdir -p "$OUTDIR"

    (cd "$GO_DIR" && go build -o "$OUTDIR/go-tools" .)

    tar -czf "$BUILD_DIR/go-tools-${PLATFORM}.tar.gz" -C "$OUTDIR" go-tools

    echo "    -> dist/go-tools-${PLATFORM}.tar.gz"
done

echo ""
echo "Build complete. Artifacts in dist/"
ls -lh "$BUILD_DIR"/*.tar.gz
