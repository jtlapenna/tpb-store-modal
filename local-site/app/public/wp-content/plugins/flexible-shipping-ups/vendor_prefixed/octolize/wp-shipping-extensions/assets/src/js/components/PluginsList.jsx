import React from 'react';
import PluginItem from "./PluginItem";

export default function PluginsList({plugins, assets_url}) {
    return <div className="oct-shipping-extensions-plugins">
        {plugins.map(plugin => <PluginItem
            key={plugin.name}
            assets_url={assets_url}
            {...plugin}/>
        )}
    </div>;
}
