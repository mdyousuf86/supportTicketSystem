<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\PredefinedReply;
use App\Http\Controllers\Controller;
use App\Models\PredefinedReplyCategory;

class PredefinedRepliesController extends Controller
{
    public function index()
    {
        $pageTitle         = "Predefined Replies";
        $predefinedReplies = PredefinedReply::orderBy('id', 'desc')->paginate(getPaginate());
        $categories        = PredefinedReplyCategory::isParent()->with('allSubcategories')->get();;

        return view('admin.replies.index', compact('pageTitle', 'predefinedReplies', 'categories'));
    }

    public function create($id)
    {
        $pageTitle = 'Crate Replies';
        $categories = PredefinedReplyCategory::with('allSubcategories')->latest()->get();

        return view('admin.replies.create', compact('pageTitle', 'reply', 'categories'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'category_id' => 'required',
            'name'        => 'required',
            'reply'       => 'required',
        ]);

        if ($id) {
            $reply   = PredefinedReply::findOrFail($id);
            $massage = 'Predefined reply message updated successfully';
        } else {
            $reply   = new PredefinedReply();
            $massage = 'Predefined reply message added successfully';
        }
        $reply->category_id = $request->category_id;
        $reply->name        = $request->name;
        $reply->reply       = $request->reply;
        $reply->save();

        $notify[] = ['success', $massage];
        return redirect()->back()->withNotify($notify);
    }

    public function status($id)
    {
        return PredefinedReply::changeStatus($id);
    }
}
