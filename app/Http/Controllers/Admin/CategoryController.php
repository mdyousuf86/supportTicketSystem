<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PredefinedReplyCategory;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $pageTitle  = "All Categories";
        $categories = $this->categoryTree();
        return view('admin.category.index', compact('pageTitle', 'categories'));
    }
    public function trashed()
    {
        $pageTitle  = "Trashed Categories";
        $categories = PredefinedReplyCategory::searchable(['name'])->onlyTrashed();
        $categories = $categories->with('allSubcategories')->orderBy('deleted_at', 'desc')->paginate(getPaginate());
        return view('admin.category.trashed', compact('pageTitle', 'categories'));
    }

    public function store(Request $request, $id = 0)
    {
        $validator = $this->validation($request, $id);

        if ($validator->fails()) {
            return errorResponse($validator->errors());
        }

          // Check if parent category exists
        if ($request->parent_id) {
            $parentCategory = PredefinedReplyCategory::with('parent')->where('id', '!=', $id)->find($request->parent_id);

            if (!$parentCategory) {
                return errorResponse('Invalid parent category selected');
            }

            if ($this->getDepthToRoot($parentCategory) >= 5) {
                return errorResponse('You have reached the maximum depth from the root category');
            }
        }

        if ($this->categoryExists($request, $id)) {
            return errorResponse('The name has already been taken');
        }

        $category = $id ?  PredefinedReplyCategory::findOrFail($id) : new PredefinedReplyCategory();

        $this->setCategoryAttributes($category, $request);
        $category->save();

        $categories    = $this->categoryTree();
        $tree          = view('admin.category.category_tree', compact('categories'))->render();
        $allCategories = view('components.category-options', ['allCategories' => $categories, 'isAdmin' => true])->render();
        $message       = $id ? 'updated' : 'added';

        return successResponse("Predefined reply category $message successfully", ['tree' => $tree, 'categoryId' => $category->id, 'allCategories' => $allCategories, 'parentId' => $category->parent_id]);
    }

    protected function setCategoryAttributes($category, $request)
    {
        $category->parent_id = $request->parent_id ?? 0;
        $category->name      = $request->name;
    }

    protected function categoryExists(Request $request, $id)
    {
        return PredefinedReplyCategory::where('id', '!=', $id)->where('name', $request->name)->where('parent_id', $request->parent_id)->exists();
    }

    protected function validation($request, $id)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|integer:gte:0',
            'name'      => 'required|string',
        ],);

        return $validator;
    }

    protected function categoryTree()
    {
        return PredefinedReplyCategory::isParent()->with('allSubcategories')->get();
    }

    public function delete($id)
    {
        $category = PredefinedReplyCategory::where('id', $id)->withTrashed()->first();

        if ($category->trashed()) {
            $category->restore();
            $notify[] = ['success', 'Predefined reply category restored successfully'];
        } else {
            $category->delete();
            $notify[] = ['success', 'Predefined reply category deleted successfully'];
        }
        return back()->withNotify($notify);
    }


    protected function getDepthToRoot(PredefinedReplyCategory $category)
    {
        $depth = 0;
        while (!blank($category->parent)) {
            $category = $category->parent;
            $depth++;
        }
        return $depth;
    }

    protected function getDepthFromRoot(PredefinedReplyCategory $category)
    {
        $maxDepth = 0;
        if ($category->allSubcategories->isNotEmpty()) {
            foreach ($category->allSubcategories as $child) {
                $childDepth = $this->getDepthFromRoot($child);
                $maxDepth   = max($maxDepth, $childDepth);
            }
        }

        return $maxDepth + 1;
    }
}
