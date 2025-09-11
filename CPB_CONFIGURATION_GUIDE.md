# CPB Configuration Guide for TPB Store Modal

## 🎯 Component Structure (Based on Strategy Canvas)

### Component 1: Number of SKUs (Hardware Kits)
**Purpose**: Sets the base price anchor for both Custom and Pre-designed flows
**Type**: Dropdown/Radio buttons
**Options**:
- 8 SKUs (Hardware Kit) - $X.XX
- 12 SKUs (Hardware Kit) - $X.XX  
- 16 SKUs (Hardware Kit) - $X.XX

**WooCommerce Products**: Use your existing hardware-only kit SKUs
**Required**: Yes (always first choice)

---

### Component 2: Build Strategy
**Purpose**: Determines if customer goes Custom or Pre-designed path
**Type**: Radio buttons
**Options**:
- Custom Build (dummy product, $0)
- Pre-designed (dummy product, $0)

**Conditional Logic**: 
- If Custom Build → Hide Components 3 & 4, lock to hardware kit SKU
- If Pre-designed → Show Components 3 & 4, enable SKU swapping

---

### Component 3: Mount Type
**Purpose**: Determines mounting configuration for Pre-designed
**Type**: Radio buttons
**Options**:
- Counter Mount
- Wall Mount  
- Free-standing

**Conditional Logic**: Only shows if Pre-designed selected
**WooCommerce Products**: Dummy products (labels only)

---

### Component 4: Finish/Material (Complete Bundles)
**Purpose**: Final SKU selection with complete pricing
**Type**: Dropdown/Radio buttons
**Options**: Complete bundle SKUs based on previous choices

**Examples**:
- Remedy – 16 SKUs – Counter – Walnut (Complete)
- Green Cloud – 12 SKUs – Free-standing – Acrylic (Complete)

**Conditional Logic**: 
- Only shows if Pre-designed + Mount selected
- Filters based on SKU count + Mount type + Manufacturer

---

## 🔧 Implementation Steps

### 1. Create Dummy Products for Strategy Choices
```php
// In WooCommerce Admin, create these products:
// - "Custom Build" (dummy, $0)
// - "Pre-designed" (dummy, $0)
// - "Counter Mount" (dummy, $0)
// - "Wall Mount" (dummy, $0)
// - "Free-standing" (dummy, $0)
```

### 2. Set Up CPB Components in Addify
1. **Component 1**: Link to hardware kit SKUs
2. **Component 2**: Link to strategy dummy products
3. **Component 3**: Link to mount dummy products  
4. **Component 4**: Link to complete bundle SKUs

### 3. Configure Conditional Logic
- Component 2 → Controls visibility of Components 3 & 4
- Component 3 → Filters Component 4 options
- Component 4 → Final SKU selection

### 4. Implement SKU Swapping Logic
```php
// Custom Build: Keep hardware kit SKU
// Pre-designed: Swap to complete bundle SKU
```

---

## 🎨 Modal Integration

The CPB will render inside your iframe modal with:
- Clean two-panel layout
- Proper styling for all components
- Responsive design
- SKU swapping on selection

---

## ✅ Next Actions

1. **Verify your WooCommerce product structure**
2. **Create dummy products for strategy choices**
3. **Set up CPB components in Addify**
4. **Configure conditional logic**
5. **Test the complete flow**

---

## 🚨 Important Notes

- **Custom Build** = Hardware Kit SKU only
- **Pre-designed** = Complete Bundle SKU (hardware + furniture)
- **Finish** = Unique SKUs, not variations
- **SKU Count** drives base price for both flows
