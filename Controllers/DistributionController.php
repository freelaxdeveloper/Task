<?php
namespace Controllers;

use Core\{Controller,Form};
use Models\Distributions;

class DistributionController extends Controller{

    public function actionIndex()
    {
        $distribution = Distributions::getDistribution();
        if ($distribution) {
            echo $distribution['link'] . ':::' . $distribution['xpath'];
            Distributions::updateInstall($distribution['id'], $distribution['install_hours']);
        } else {
            echo 'shutdown';
        }
    }

    public function actionAdmin()
    {
        $distributions = Distributions::getDistributions();
        if (isset($_POST['clear'])) {
            $q = \Core\DB::me()->query("DELETE FROM `distribution_install`");
            header('Location: ' . \Core\App::referer());
        }
        $form = new \Core\Form('/distribution/admin/');
        $form->submit(['value' => 'Очистить', 'name' => 'clear']);
        $this->params['form_clear'] = $form->display();

        $this->params['distributions'] = $distributions;
        $this->display('distribution/list');
    }
    public function actionEdit(int $id)
    {
        $distributions = Distributions::getDistributions(['id' => $id]);
        if (!$distributions) {
            \Core\App::access_denied(__('Ошибка'));
        }
        $hours = Distributions::getSettings($id);
        $percents = \Core\DB::me()->query("SELECT * FROM `distribution_percent` WHERE `id_distribution` = '$id'")->fetchAll();
        
        if (isset($_POST['save'])) {
            $hours = $_POST['hours'];
            for ($i = 0; $i < count($hours); $i++) {
                if (!$hours[$i]) {
                    $hours[$i] = 0;
                }
                Distributions::updateSettings($id, $i, $hours[$i]);
            }
            header('Location: ' . \Core\App::url('/distribution/admin/edit/' . $id . '/'));
            exit;
        }
        if (isset($_POST['save_percents'])) {
            $percents = $_POST['percents'];
            for ($i = 0; $i < count($percents); $i++) {
                if (!$percents[$i]) {
                    $percents[$i] = 0;
                }
                Distributions::updatePercents($id, $i, $percents[$i]);
            }
            header('Location: ' . \Core\App::url('/distribution/admin/edit/' . $id . '/'));
            exit;
        }
        if (isset($_POST['cancel'])) {
            header('Location: ' . \Core\App::url('/distribution/admin/'));
            exit;
        }
        if (isset($_POST['save_percent'])) {
            $count = (int) $_POST['count'];
            foreach ($percents as $percent) {
                $count_install = ceil($count / 100 * $percent['percent']);
                if (!$count_install) {
                    $count_install = 0;
                }
                Distributions::updateSettings($id, $percent['hours'], $count_install);
            }
            header('Location: ' . \Core\App::url('/distribution/admin/edit/' . $id . '/'));
            exit;
        }
        $form = new Form('/distribution/admin/edit/' . $id . '/');
        foreach (range(0, 23) as $number) {
            $value = $hours[$number]['count'] ?? false;
            $form->input(['name' => 'hours[]', 'holder' => $number . ' час...', 'value' => $value, 'br' => $number < 23 ? false : true]);
        }
        $form->submit(['value' => 'Редактировать', 'name' => 'save', 'br' => false]);
        $form->submit(['value' => 'Отмена', 'name' => 'cancel']);
        $this->params['form_edit'] = $form->display();

        $form = new Form('/distribution/admin/edit/' . $id . '/');
        $form->input(['name' => 'count', 'value' => $distributions['install_all']]);
        $form->submit(['value' => 'Сохранить', 'name' => 'save_percent', 'br' => false]);
        $form->submit(['value' => 'Отмена', 'name' => 'cancel']);
        $this->params['form_edit_percent'] = $form->display();

        $form = new Form('/distribution/admin/edit/' . $id . '/');
        foreach (range(0, 23) as $number) {
            $value = $percents[$number]['percent'] ?? false;
            $form->input(['name' => 'percents[]', 'holder' => $number . ' час... (в %)', 'value' => $value, 'br' => $number < 23 ? false : true]);
        }
        $form->submit(['value' => 'Редактировать', 'name' => 'save_percents', 'br' => false]);
        $form->submit(['value' => 'Отмена', 'name' => 'cancel']);
        $this->params['form_edit_percents'] = $form->display();

        $this->params['title'] = __('Редактирование - %s', $distributions['title']);
        $this->params['distribution'] = $distributions;
        $this->display('distribution/edit');
    }
}