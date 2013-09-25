<?php

namespace FrontModule;

use Nette\Application\Responses\FileResponse;

/**
 * Covering needs of web presentation (fronModul).
 * 
 * @author Radek Tobolka
 */
class HomepagePresenter extends BasePresenter {

    private $assets;
    private $files;
    private $wholesales;

    /**
     * Preapring services, genereating menus
     */
    public function startup() {
        parent::startup();
        $this->assets = $this->getService('asset');
        $this->files = $this->getService('file');
        $this->wholesales = $this->getService('wholesale');

        $this->template->lang = $this->lang;

        $this->template->menu = $this->createMenu($this->assets->all($this->lang));
        $this->template->sidebar = $this->assets->sidebar($this->lang)->content;
    }

    /**
     * Generating menu array from items in DB.
     * 
     * @param array $assets
     * @return array
     */
    private function createMenu($assets) {
        $menu = array();

        foreach ($assets as $asset) {
            if ($asset->parent == 0) {
                if ($asset->type === 'category') {
                    $subMenu = array();
                    foreach ($assets as $item) {
                        if ($item->parent == $asset->id) {
                            if ($item->type == 'link') {
                                $url = $item->content;
                            } else {
                                $url = $this->link('Homepage:default', $item->url);
                            }
                            $subMenu[$item->title] = $url;
                        }
                    }
                    $menu[$asset->title] = $subMenu;
                } else {
                    $menu[$asset->title] = $this->link('Homepage:default', $asset->url);
                }
            }
        }

        return $menu;
    }

    /**
     * Default rendering
     * 
     * @param string $url
     */
    public function renderDefault($url) {
        if ($url == 'seznam-velkoobchodu' or $url == 'list-of-wholesales') {
            $this->setView('wholesale');
            $this->template->groups = $this->wholesales();
        }

        $this->template->asset = $this->assets->findUrl($url, $this->lang);
    }

    /**
     * Generating array of wholesales from DB.
     *  
     * @return array
     */
    private function wholesales() {
        $wholesales = $this->wholesales->all();

        $groups = array();
        $group_id_prev = 0;

        foreach ($wholesales as $wholesale) {

            $group_id = $wholesale->group;

            if ($group_id_prev != $group_id) {
                $groups[$wholesale->group] = array(
                    'id' => $wholesale->group,
                    'city' => $wholesale->city,
                    'street' => $wholesale->street,
                    'url' => $wholesale->url,
                    'name' => $wholesale->name,
                );
            }

            $item = array(
                'id' => $wholesale->id,
                'name' => $wholesale->name,
                'city' => $wholesale->city,
                'street' => $wholesale->street,
                'type' => $wholesale->type,
            );

            $groups[$wholesale->group]['items'][] = $item;

            $group_id_prev = $group_id;
        }

        return $groups;
    }

    /**
     * Handling PDF for download.
     * 
     * @param string $name
     * @return pdf $pdf
     */
    public function handleDownload($name) {
        $file = $this->files->find($name);
        $pdf = WWW_DIR . '/files/' . $file->path;
        $file = @file_get_contents($pdf);

        if ($file) {
            $this->sendResponse(new FileResponse($pdf));
        }
    }

    /**
     * Handling details for wholesales ajax map.
     * 
     * @param int $param
     */
    public function handleMap($param) {
        if ($param > 1000) {
            $param -=1000;
        }

        $group_id = $this->wholesales->item($param)->fetch()->group;
        $group = $this->wholesales->group($group_id);

        $this->template->map_detail = $group;

        $this->invalidateControl('snippetMap');
    }

    /**
     *  Generating array for flash map of wholesales.
     */
    public function renderMapXml() {
        $wholesales = $this->wholesales->all();

        $groups = array();
        $group_id_prev = 0;

        foreach ($wholesales as $wholesale) {

            $group_id = $wholesale->group;

            if ($group_id_prev != $group_id) {
                $groups[$wholesale->group] = array(
                    'id' => $wholesale->group,
                    'city' => $wholesale->city,
                    'street' => $wholesale->street,
                    'url' => $wholesale->url,
                );
            }

            $item = array(
                'id' => $wholesale->id,
                'name' => $wholesale->name,
                'city' => $wholesale->city,
                'street' => $wholesale->street,
                'url' => $wholesale->url,
                'type' => $wholesale->type,
                'x' => $wholesale->x,
                'y' => $wholesale->y,
            );

            if ($wholesale->type == 1) {
                $item['type'] = 2;

                $groups[$wholesale->group]['items'][] = $item;

                $item['id'] = $wholesale->id + 1000;
                $item['type'] = 4;
                $item['x'] = $wholesale->x - 2;
                $item['y'] = $wholesale->y + 2;
            }

            $groups[$wholesale->group]['items'][] = $item;
            $group_id_prev = $group_id;
        }

        $this->template->groups = $groups;
    }

}
