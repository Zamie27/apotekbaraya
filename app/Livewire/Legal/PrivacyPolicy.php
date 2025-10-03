<?php

namespace App\Livewire\Legal;

use Livewire\Component;
use App\Models\StoreSetting;

class PrivacyPolicy extends Component
{
    // Store contact information properties
    public $storeName;
    public $storeEmail;
    public $storePhone;
    public $storeWhatsapp;
    public $storeAddress;
    public $storeVillage;
    public $storeDistrict;
    public $storeRegency;
    public $storeProvince;
    public $storePostalCode;

    /**
     * Load store settings and set the page title
     */
    public function mount()
    {
        $this->loadStoreSettings();
        $this->title = 'Kebijakan Privasi - ' . $this->storeName;
    }

    /**
     * Load store contact information from settings
     */
    private function loadStoreSettings()
    {
        $this->storeName = StoreSetting::get('store_name', 'Apotek Baraya');
        $this->storeEmail = StoreSetting::get('store_email', 'info@apotekbaraya.com');
        $this->storePhone = StoreSetting::get('store_phone', '+62812345678');
        $this->storeWhatsapp = StoreSetting::get('store_whatsapp', '+62812345678');
        $this->storeAddress = StoreSetting::get('store_address', '');
        $this->storeVillage = StoreSetting::get('store_village', '');
        $this->storeDistrict = StoreSetting::get('store_district', '');
        $this->storeRegency = StoreSetting::get('store_regency', '');
        $this->storeProvince = StoreSetting::get('store_province', '');
        $this->storePostalCode = StoreSetting::get('store_postal_code', '');
    }

    /**
     * Get formatted full address
     */
    public function getFullAddressProperty()
    {
        $addressParts = array_filter([
            $this->storeAddress,
            $this->storeVillage,
            $this->storeDistrict,
            $this->storeRegency,
            $this->storeProvince,
            $this->storePostalCode
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Render the privacy policy page
     */
    public function render()
    {
        return view('livewire.legal.privacy-policy')
            ->layout('components.layouts.user')
            ->title('Kebijakan Privasi - ' . $this->storeName);
    }
}