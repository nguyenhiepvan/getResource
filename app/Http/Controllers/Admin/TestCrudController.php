<?php
namespace App\Http\Controllers\Admin;
use Backpack\CRUD\app\Http\Controllers\CrudController;
// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\TestRequest as StoreRequest;
use App\Http\Requests\Request;
use App\Http\Requests\TestRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
// use Illuminate\Support\Facades\Cache;
use App\Models\Test;
use App\Models\Subject;
use App\Models\Question;
/**
 * Class TestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class TestCrudController extends CrudController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        $this->crud->setModel('App\Models\Test');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/test');
        $this->crud->setEntityNameStrings('test', 'tests');
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */
        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();
        $this->crud->addButton('top', 'demo', 'btn btn-default', 'admin.test.button', 'beginning');
        // $this->crud->setCreateView('admin.test.addTest');
        // $this->crud->setCreateView('admin.test.addTest');
        // add asterisk for fields that are required in TestRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
    }
    public function create()
    {
        $this->crud->hasAccessOrFail('create');
        $this->crud->setOperation('create');
    $this->crud->addField([   // CKEditor
        'name' => 'title',
        'label' => 'Title',
        'type' => 'ckeditor',
    // optional:install
        'extra_plugins' => ['oembed', 'widget'],
        'options' => [
            'autoGrow_minHeight' => 200,
            'autoGrow_bottomSpace' => 50,
            'removePlugins' => 'resize,maximize',
        ]
    ]);
        // $tests = Test::whereNull('deleted_at')->select('title','id')->get();
    $subjects = Subject::whereNull('deleted_at')->select('name','id')->get();
    $questions = Question::whereNull('deleted_at')->select('content','id')->get();
    $options_subject = array();
    foreach ($subjects as $subject) {
        $options_subject[$subject->id] = $subject->name;
    }
        // dd($options_subject);
    $options_question = array();
    foreach ($questions as $question) {
        $options_question[$question->id] = $question->content;
    }
        $this->crud->addField([ // select_from_array
            'name' => 'subject_id',
            'label' => "Subject",
            'type' => 'select2_from_array',
            'options' => $options_subject,
            'allows_null' => false,
            'default' => '',
        ]);
        $this->crud->addField([ // select_from_array
            'name' => 'question_id',
            'label' => "Question",
            'type' => 'select2_from_array',
            'options' => $options_question,
            'allows_null' => false,
            'default' => '',
            'allows_multiple' => true,
        ]);
        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->getSaveAction();
        $this->data['fields'] = $this->crud->getCreateFields();
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.add').' '.$this->crud->entity_name;
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getCreateView(), $this->data);
    }
    public function store(StoreRequest $request)
    {
        $this->crud->hasAccessOrFail('create');
        $this->crud->setOperation('create');
        // fallback to global request instance
        if (is_null($request)) {
            $request = \Request::instance();
        }
        // insert item in the db
        $item = $this->crud->create($request->except(['save_action', '_token', '_method', 'current_tab', 'http_referrer','question_id']));
        $this->data['entry'] = $this->crud->entry = $item;
        foreach ($request->question_id as $id) {
           \DB::table('test_questions')->insert(['question_id'=>$id,'test_id'=>$item->id]);
       }
        // show a success message
       \Alert::success(trans('backpack::crud.insert_success'))->flash();
        // save the redirect choice for next time
       $this->setSaveAction();
       return $this->performSaveAction($item->getKey());
   }
   public function update(UpdateRequest $request)
   {
        // your additional operations before save here
    $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
    return $redirect_location;
}
}