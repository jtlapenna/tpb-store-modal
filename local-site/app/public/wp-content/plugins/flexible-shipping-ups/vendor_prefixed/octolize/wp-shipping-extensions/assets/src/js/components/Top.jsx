import React from 'react';

export default function Top({assets_url, header_title, header_description, header_promo = []}) {
    const url = `${assets_url}img/logo-black.svg`;

    return <>
        <section className="oct-shipping-extensions-top">
            <h1>{header_title} <img alt="Octolize" src={url}/></h1>
            <p>{header_description}</p>
            {header_promo.map(promo => {
                const markup = { __html: promo };
                return <p className="oct-promo" dangerouslySetInnerHTML={markup}></p>
            })}
        </section>

        <div className="oct-shipping-extensions-notice-list-hide">
            <div className="wp-header-end"></div>
        </div>
    </>;
}
