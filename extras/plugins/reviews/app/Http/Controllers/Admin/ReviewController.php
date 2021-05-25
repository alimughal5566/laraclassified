<?php

namespace extras\plugins\reviews\app\Http\Controllers\Admin;

use extras\plugins\reviews\app\Models\Post;
use Larapen\Admin\app\Http\Controllers\PanelController;

use extras\plugins\reviews\app\Http\Requests\ReviewRequest as StoreRequest;
use extras\plugins\reviews\app\Http\Requests\ReviewRequest as UpdateRequest;

class ReviewController extends PanelController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->xPanel->setModel('extras\plugins\reviews\app\Models\Review');
        $this->xPanel->setRoute(admin_uri('reviews'));
        $this->xPanel->setEntityNameStrings(strtolower(trans('reviews::messages.Review')), strtolower(trans('reviews::messages.Reviews')));
        $this->xPanel->denyAccess(['create']);
		if (!request()->input('order')) {
			$this->xPanel->orderBy('created_at', 'DESC');
		}
	
		$this->xPanel->addButtonFromModelFunction('top', 'bulk_delete_btn', 'bulkDeleteBtn', 'end');

        /*
        |--------------------------------------------------------------------------
        | COLUMNS AND FIELDS
        |--------------------------------------------------------------------------
        */
        // COLUMNS
		$this->xPanel->addColumn([
			'name'  => 'id',
			'label' => '',
			'type'  => 'checkbox',
			'orderable' => false,
		]);
        $this->xPanel->addColumn([
            'name'  => 'created_at',
            'label' => trans("admin::messages.Date"),
        ]);
        $this->xPanel->addColumn([
            'name'          => 'post_id',
            'label'         => trans("reviews::messages.Ad"),
            'type'          => 'model_function',
            'function_name' => 'getPostTitleHtml',
        ]);
        $this->xPanel->addColumn([
            'name'          => 'user_id',
            'label'         => trans("reviews::messages.User"),
            'type'          => 'model_function',
            'function_name' => 'getUserHtml',
        ]);
        $this->xPanel->addColumn([
            'name'  => 'rating',
            'label' => trans("reviews::messages.Rating"),
        ]);
        $this->xPanel->addColumn([
            'name'  => 'comment',
            'label' => trans("reviews::messages.Comment"),
        ]);
        $this->xPanel->addColumn([
            'name'          => 'approved',
            'label'         => trans("reviews::messages.Approved"),
            'type'          => 'model_function',
            'function_name' => 'getApprovedHtml',
        ]);

        // FIELDS
        $this->xPanel->addField([
            'name'       => 'comment',
            'label'      => trans('reviews::messages.Comment'),
            'type'       => 'textarea',
            'attributes' => [
                'placeholder' => trans('reviews::messages.Comment'),
            ],
        ]);
        $this->xPanel->addField([
            'name'  => 'approved',
            'label' => trans("reviews::messages.Approved"),
            'type'  => 'checkbox',
        ]);
    }

    public function store(StoreRequest $request)
    {
        return parent::storeCrud();
    }

    public function update(UpdateRequest $request)
    {
        try {
            $this->xPanel->hasAccessOrFail('update');

            // fallback to global request instance
            if (is_null($request)) {
                $request = \Request::instance();
            }

            // replace empty values with NULL, so that it will work with MySQL strict mode on
            foreach($request->input() as $key => $value) {
                if (empty($value) && $value !== '0') {
                    $request->request->set($key, null);
                }
            }

            // update the row in the db
            $this->xPanel->update($request->get($this->xPanel->model->getKeyName()), $request->except('redirect_after_save', '_token'));

            // Recalculate ratings for the specified product
            $reviewsId = $request->get($this->xPanel->model->getKeyName());
            $review = $this->xPanel->getEntry($reviewsId);
            $post = Post::find($review->post_id);
            if (!empty($post)) {
                $post->recalculateRating();
            }

            // show a success message
            \Alert::success(trans('admin::messages.update_success'))->flash();

            return \Redirect::to($this->xPanel->route);
        } catch (\Exception $e) {
            // Get error message
            if (isset($e->errorInfo) && isset($e->errorInfo[2]) && !empty($e->errorInfo[2])) {
                $msg = $e->errorInfo[2];
            } else {
                $msg = $e->getMessage();
            }

            // Error notification
            \Alert::error('Error found - [' . $e->getCode() . '] : ' . $msg . '.')->flash();

            return \Redirect::to($this->xPanel->route);
        }
    }

    public function destroy($id, $childId = null)
    {
        $this->xPanel->hasAccessOrFail('delete');

        if (!empty($childId)) {
            $id = $childId;
        }

        $review = $this->xPanel->getEntry($id);
        $postId = $review->post_id;

        // Delete the entry
        $res = $this->xPanel->delete($id);

        // Recalculate ratings for the specified product
        $post = Post::find($postId);
        if (empty($post)) {
            return false;
        }
        $post->recalculateRating();

        return $res;
    }
}
