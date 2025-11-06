import React, {useState} from 'react';
import Header from "./Header";
import Top from "./Top";
import CategoryFilter from "./CategoryFilter";
import PluginsList from "./PluginsList";

export default function ShippingExtensions(props) {
    const {
        assets_url,
        admin_page_title,
        header_title,
        header_description,
        header_promo,
        plugins,
        text_filter,
        default_category,
        categories
    } = props;

    const [activeCategory, setActiveCategory] = useState(default_category);

    const handleCategoryClick = (activeCategory) => {
        setActiveCategory(activeCategory);
    }

    return <div className="oct-shipping-extensions">
        <Header title={admin_page_title}/>

        <div className="oct-shipping-extension-content-wrapper">
            <Top
                assets_url={assets_url}
                header_title={header_title}
                header_description={header_description}
                header_promo={header_promo ?? []}
            />

            <CategoryFilter
                textFilter={text_filter}
                activeCategory={activeCategory}
                categories={categories}
                filterCategory={handleCategoryClick}
            />

            <PluginsList
                assets_url={assets_url}
                plugins={plugins.filter(plugin => {
                    return activeCategory === default_category || plugin.category === activeCategory;
                })}/>
        </div>
    </div>;
}
