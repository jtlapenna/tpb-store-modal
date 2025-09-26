/**
 * CPB Diagnostic Script
 * Run this in the browser console on the staging site to diagnose CPB issues
 */

console.log('🔍 CPB Diagnostic Script Starting...');

// Check 1: Plugin Status
console.log('\n=== PLUGIN STATUS ===');
const addifyScript = document.querySelector('script[src*="af-comp-product"]');
console.log('Addify CPB Script Found:', !!addifyScript);
if (addifyScript) {
    console.log('Script URL:', addifyScript.src);
} else {
    console.log('❌ Addify CPB script not found - plugin may not be active');
}

// Check 2: WooCommerce Status
console.log('\n=== WOOCOMMERCE STATUS ===');
const wcScript = document.querySelector('script[src*="woocommerce"]');
console.log('WooCommerce Script Found:', !!wcScript);
const productElement = document.querySelector('.woocommerce div.product');
console.log('Product Element Found:', !!productElement);

// Check 3: CPB Components
console.log('\n=== CPB COMPONENTS ===');
const cpbContainer = document.querySelector('.af_cp_all_components_content');
console.log('CPB Container Found:', !!cpbContainer);
const cpbComponents = document.querySelectorAll('.af_cp_all_components_content .single_component');
console.log('CPB Components Count:', cpbComponents.length);

// Check 4: Alternative Elements
console.log('\n=== ALTERNATIVE ELEMENTS ===');
const variations = document.querySelectorAll('.variations');
console.log('WooCommerce Variations:', variations.length);
const selects = document.querySelectorAll('select:not([name="quantity"])');
console.log('Select Elements:', selects.length);
const forms = document.querySelectorAll('form');
console.log('Form Elements:', forms.length);

// Check 5: Product Data
console.log('\n=== PRODUCT DATA ===');
const productDataScripts = document.querySelectorAll('script[type="application/json"]');
console.log('Product Data Scripts:', productDataScripts.length);
productDataScripts.forEach((script, index) => {
    try {
        const data = JSON.parse(script.textContent);
        console.log(`Product Data ${index + 1}:`, data);
    } catch (e) {
        console.log(`Product Data ${index + 1} Parse Error:`, e.message);
    }
});

// Check 6: Page Parameters
console.log('\n=== PAGE PARAMETERS ===');
const urlParams = new URLSearchParams(window.location.search);
console.log('URL Parameters:', Object.fromEntries(urlParams));
console.log('tpb_qv_staging:', urlParams.get('tpb_qv_staging'));

// Check 7: Body Classes
console.log('\n=== BODY CLASSES ===');
console.log('Body Classes:', document.body.className);
console.log('Has tpb-qv class:', document.body.classList.contains('tpb-qv'));

// Check 8: Available Scripts
console.log('\n=== AVAILABLE SCRIPTS ===');
const allScripts = Array.from(document.querySelectorAll('script[src]'));
const relevantScripts = allScripts.filter(script => 
    script.src.includes('addify') || 
    script.src.includes('cpb') || 
    script.src.includes('woocommerce') ||
    script.src.includes('product')
);
console.log('Relevant Scripts:', relevantScripts.map(s => s.src));

// Check 9: TPB Modal Status
console.log('\n=== TPB MODAL STATUS ===');
const tpbScripts = allScripts.filter(script => script.src.includes('tpb'));
console.log('TPB Scripts Found:', tpbScripts.length);
tpbScripts.forEach(script => console.log('  -', script.src));

// Check 10: Iframe Content
console.log('\n=== IFRAME CONTENT ===');
console.log('Is in iframe:', window !== window.parent);
console.log('Document title:', document.title);
console.log('Body content length:', document.body.innerHTML.length);
console.log('Body classes:', document.body.className);

console.log('\n🔍 CPB Diagnostic Complete!');

