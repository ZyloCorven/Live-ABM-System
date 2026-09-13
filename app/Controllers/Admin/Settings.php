<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class Settings extends BaseController
{
    protected SettingModel $settings;

    public function __construct()
    {
        $this->settings = new SettingModel();
    }

    public function index()
    {
        return view('admin/settings', [
            'title'    => 'Auction Settings',
            'settings' => $this->settings->findAll(),
        ]);
    }

    public function update()
    {
        $this->settings->set('extension_window_minutes', (string) (int) $this->request->getPost('extension_window_minutes'));
        $this->settings->set('extension_duration_minutes', (string) (int) $this->request->getPost('extension_duration_minutes'));

        return redirect()->to('/admin/settings')->with('success', 'Settings updated.');
    }
}
