<?php

namespace UpsFreeVendor\Octolize\Blocks\PickupPoint\MapPickupPoint;

use UpsFreeVendor\Octolize\Blocks\PickupPoint\IntegrationData;
class MapIntegrationData extends IntegrationData
{
    const POINTS_LIMIT = 'pointsLimit';
    const DISPLAY_LIST = 'displayList';
    const DISPLAY_MAP = 'displayMap';
    const DISPLAY_POPUP = 'displayPopup';
    public function set_points_limit(int $points_limit): MapIntegrationData
    {
        $this->set_data(self::POINTS_LIMIT, $points_limit);
        return $this;
    }
    public function get_points_limit(): int
    {
        return $this->get_data(self::POINTS_LIMIT);
    }
    public function set_display_list(bool $display_list): MapIntegrationData
    {
        $this->set_data(self::DISPLAY_LIST, $display_list);
        return $this;
    }
    public function get_display_list(): bool
    {
        return $this->get_data(self::DISPLAY_LIST);
    }
    public function set_display_map(bool $display_map): MapIntegrationData
    {
        $this->set_data(self::DISPLAY_MAP, $display_map);
        return $this;
    }
    public function get_display_map(): bool
    {
        return $this->get_data(self::DISPLAY_MAP);
    }
    public function set_display_popup(bool $display_popup): MapIntegrationData
    {
        $this->set_data(self::DISPLAY_POPUP, $display_popup);
        return $this;
    }
    public function get_display_popup(): bool
    {
        return $this->get_data(self::DISPLAY_POPUP);
    }
}
