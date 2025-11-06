import {createRoot} from 'react-dom/client'
import ShippingExtensions from './components/ShippingExtensions';

const elem = document.getElementById('shipping-extensions');

document.body.addEventListener('click', function (evt) {
    if (evt.target.classList.contains('oct-copy-to-clipboard') ) {
        window.navigator.clipboard.writeText(evt.target.dataset.value).then(function () {
            evt.target.classList.add('oct-copied');
            setTimeout(function () {
                evt.target.classList.remove('oct-copied');
            }, 2000);
        });
    }
}, false);

const root = createRoot(elem);
root.render(<ShippingExtensions
    {...elem.dataset}
    categories={JSON.parse(elem.dataset.categories)}
    plugins={JSON.parse(elem.dataset.plugins)}
    header_promo={JSON.parse(elem.dataset.header_promo)}
/>);
