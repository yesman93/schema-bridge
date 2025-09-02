<?php

namespace App\Controllers\Lumio;

use App\Models\TestsModel;
use App\Models\UserModel;
use Lumio\Controller\BaseController;
use Lumio\Database\DatabaseAdapter;
use Lumio\IO\Request;
use Lumio\IO\Response;
use Lumio\Model\BaseModel;
use Lumio\Storage\Path;
use Lumio\View\View;

class TestsController extends BaseController {

    /**
     * Constroller for application tests in isolation
     *
     * @author TB
     * @date 17.5.2025
     *
     * @param BaseModel|null $model
     * @param Request $request
     * @param Response $response
     * @param View $view
     *
     * @return void
     */
    public function __construct(?BaseModel $model, Request $request, Response $response, View $view) {
        parent::__construct($model, $request, $response, $view);
    }

    public function devel() {

        $this->title(__tx('Development'));

//        return $this->redirect('https://www.google.com');



        if (!$this->is_submit()) {
            return false;
        }


        $file = $this->file('fname');

        $new_name = 'uploaded-file-' . date('Ymd-His') . '.' . $file->get_extension();
        $save = $file->save(Path::uploads(), $new_name);

        if ($save) {
            $redirect = $this->redirect('/lumio/tests/devel')->success(__tx('File uploaded successfully: %s', $file->get_name()));
        } else {
            $redirect = $this->redirect('/lumio/tests/devel')->error(__tx('File upload failed!'));
        }

        return $redirect;

    }

    public function modal() {

        $this->master(View::MASTER_MODAL);
        $this->title(__tx('Modal test page'));

    }

    public function dwl() {

        $fpath = Path::uploads() . '/file-20250530-094316.jpg';

        return $this->download_file($fpath);

    }

    public function benchmark_routing() {

        echo 'hello';
        exit;

    }

    public function benchmark_model() {

        $model = new TestsModel();
        $data = $model->benchmark_simple();

        echo $data;
        exit;

    }

    public function benchmark_database() {

        $adapter = $this->container()->get(DatabaseAdapter::class);
        $model = new UserModel($adapter);
        $user = $model->get(1);

        echo $user['first_name'] . ' ' . $user['last_name'];
        exit;

    }

    public function benchmark() {

        $this->set_render(false);


        benchmark('Routing', 'http://lumio.test/lumio/tests/benchmark_routing');
        benchmark('Model', 'http://lumio.test/lumio/tests/benchmark_model');
        benchmark('Database', 'http://lumio.test/lumio/tests/benchmark_database');


        exit;
    }

}
