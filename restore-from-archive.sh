#!/bin/bash
#
# Option 1: Clean Restart - Automated Restore Script
# 
# This script automates the restoration of the modal system from the archived
# version (from-commit-5c0d229-final). It:
# 1. Archives the current broken version
# 2. Restores the archive version
# 3. Verifies the restoration
#
# Usage:
#   ./restore-from-archive.sh
#
# Requirements:
#   - Archive must exist at: archive/from-commit-5c0d229-final/
#   - Current plugin at: local-site/app/public/wp-content/plugins/tpb-quickview-modal/
#   - Current mu-plugin at: local-site/app/public/wp-content/mu-plugins/tpb-quickview/
#
# Created: November 5, 2025

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Project root directory
PROJECT_ROOT="/Users/jeff/Projects/tpb-store-modal"
cd "$PROJECT_ROOT" || exit 1

# Paths
ARCHIVE_DIR="archive/from-commit-5c0d229-final"
CURRENT_PLUGIN="local-site/app/public/wp-content/plugins/tpb-quickview-modal"
CURRENT_MU_PLUGIN="local-site/app/public/wp-content/mu-plugins/tpb-quickview"
ARCHIVE_PLUGIN="$ARCHIVE_DIR/plugins-tpb-quickview-modal"
ARCHIVE_MU_PLUGIN="$ARCHIVE_DIR/mu-plugins-tpb-quickview"

# Timestamp for backup
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_DIR="archive/current-broken-version-$TIMESTAMP"

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}Option 1: Clean Restart - Restore Script${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Step 1: Verify archive exists
echo -e "${YELLOW}Step 1: Verifying archive exists...${NC}"
if [ ! -d "$ARCHIVE_DIR" ]; then
    echo -e "${RED}ERROR: Archive directory not found: $ARCHIVE_DIR${NC}"
    exit 1
fi

if [ ! -d "$ARCHIVE_PLUGIN" ]; then
    echo -e "${RED}ERROR: Archive plugin not found: $ARCHIVE_PLUGIN${NC}"
    exit 1
fi

if [ ! -d "$ARCHIVE_MU_PLUGIN" ]; then
    echo -e "${RED}ERROR: Archive mu-plugin not found: $ARCHIVE_MU_PLUGIN${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Archive verified${NC}"
echo ""

# Step 2: Archive current broken version
echo -e "${YELLOW}Step 2: Archiving current broken version...${NC}"

if [ -d "$CURRENT_PLUGIN" ] || [ -d "$CURRENT_MU_PLUGIN" ]; then
    mkdir -p "$BACKUP_DIR"
    
    if [ -d "$CURRENT_PLUGIN" ]; then
        echo "  Archiving plugin..."
        cp -r "$CURRENT_PLUGIN" "$BACKUP_DIR/plugins-tpb-quickview-modal"
        echo -e "${GREEN}  ✓ Plugin archived to: $BACKUP_DIR/plugins-tpb-quickview-modal${NC}"
    else
        echo -e "${YELLOW}  ⚠ Current plugin not found (may have been removed)${NC}"
    fi
    
    if [ -d "$CURRENT_MU_PLUGIN" ]; then
        echo "  Archiving mu-plugin..."
        cp -r "$CURRENT_MU_PLUGIN" "$BACKUP_DIR/mu-plugins-tpb-quickview"
        echo -e "${GREEN}  ✓ MU-plugin archived to: $BACKUP_DIR/mu-plugins-tpb-quickview${NC}"
    else
        echo -e "${YELLOW}  ⚠ Current mu-plugin not found (may have been removed)${NC}"
    fi
    
    echo -e "${GREEN}✓ Current version archived to: $BACKUP_DIR${NC}"
else
    echo -e "${YELLOW}⚠ No current version found to archive (proceeding with restore)${NC}"
fi
echo ""

# Step 3: Remove current versions
echo -e "${YELLOW}Step 3: Removing current versions...${NC}"

if [ -d "$CURRENT_PLUGIN" ]; then
    echo "  Removing current plugin..."
    rm -rf "$CURRENT_PLUGIN"
    echo -e "${GREEN}  ✓ Current plugin removed${NC}"
else
    echo -e "${YELLOW}  ⚠ Current plugin not found (already removed?)${NC}"
fi

if [ -d "$CURRENT_MU_PLUGIN" ]; then
    echo "  Removing current mu-plugin..."
    rm -rf "$CURRENT_MU_PLUGIN"
    echo -e "${GREEN}  ✓ Current mu-plugin removed${NC}"
else
    echo -e "${YELLOW}  ⚠ Current mu-plugin not found (already removed?)${NC}"
fi
echo ""

# Step 4: Restore from archive
echo -e "${YELLOW}Step 4: Restoring from archive...${NC}"

# Restore plugin
echo "  Restoring plugin..."
mkdir -p "$(dirname "$CURRENT_PLUGIN")"
cp -r "$ARCHIVE_PLUGIN" "$CURRENT_PLUGIN"
echo -e "${GREEN}  ✓ Plugin restored${NC}"

# Restore mu-plugin
echo "  Restoring mu-plugin..."
mkdir -p "$(dirname "$CURRENT_MU_PLUGIN")"
cp -r "$ARCHIVE_MU_PLUGIN" "$CURRENT_MU_PLUGIN"
echo -e "${GREEN}  ✓ MU-plugin restored${NC}"

echo -e "${GREEN}✓ Restoration complete${NC}"
echo ""

# Step 5: Verify restoration
echo -e "${YELLOW}Step 5: Verifying restoration...${NC}"

VERIFY_FAILED=0

if [ ! -d "$CURRENT_PLUGIN" ]; then
    echo -e "${RED}  ✗ Plugin not found after restore${NC}"
    VERIFY_FAILED=1
else
    # Check for key files
    if [ -f "$CURRENT_PLUGIN/tpb-quickview-modal.php" ]; then
        echo -e "${GREEN}  ✓ Plugin main file exists${NC}"
    else
        echo -e "${RED}  ✗ Plugin main file missing${NC}"
        VERIFY_FAILED=1
    fi
    
    if [ -d "$CURRENT_PLUGIN/assets/js" ]; then
        echo -e "${GREEN}  ✓ Plugin JS directory exists${NC}"
    else
        echo -e "${RED}  ✗ Plugin JS directory missing${NC}"
        VERIFY_FAILED=1
    fi
    
    if [ -d "$CURRENT_PLUGIN/assets/css" ]; then
        echo -e "${GREEN}  ✓ Plugin CSS directory exists${NC}"
    else
        echo -e "${RED}  ✗ Plugin CSS directory missing${NC}"
        VERIFY_FAILED=1
    fi
fi

if [ ! -d "$CURRENT_MU_PLUGIN" ]; then
    echo -e "${RED}  ✗ MU-plugin not found after restore${NC}"
    VERIFY_FAILED=1
else
    if [ -f "$CURRENT_MU_PLUGIN/load.php" ]; then
        echo -e "${GREEN}  ✓ MU-plugin main file exists${NC}"
    else
        echo -e "${RED}  ✗ MU-plugin main file missing${NC}"
        VERIFY_FAILED=1
    fi
fi

if [ $VERIFY_FAILED -eq 1 ]; then
    echo ""
    echo -e "${RED}✗ Verification failed - please check manually${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Verification passed${NC}"
echo ""

# Summary
echo -e "${BLUE}========================================${NC}"
echo -e "${GREEN}Restoration Complete!${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""
echo "Next steps:"
echo "  1. Set Elementor settings (see OPTION_1_EXECUTION_PLAN.md Step 3)"
echo "  2. Clear all caches"
echo "  3. Test the modal system"
echo ""
echo "Current version backed up to:"
echo "  $BACKUP_DIR"
echo ""
echo "Restored from:"
echo "  $ARCHIVE_DIR"
echo ""

