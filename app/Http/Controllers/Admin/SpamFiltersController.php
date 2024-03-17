<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SpamFilter;
use Illuminate\Http\Request;

class SpamFiltersController extends Controller
{
    public function index()
    {
        $pageTitle   = "Spam Filters";
        $spamFilters = SpamFilter::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.spam_filter.index', compact('pageTitle', 'spamFilters'));
    }

    public function save(Request $request, $id = 0)
    {
        $request->validate([
            'filter_type' => 'required',
            'content'     => 'required',
        ]);

            if ($id) {
                $spamFilter = SpamFilter::findOrFail($id);
                $massage    = 'Spam filter updated successfully';
            } else {
                $spamFilter = new SpamFilter();
                $massage    = 'Spam filter added successfully';
            }

        $spamFilter->filter_type = $request->filter_type;
        $spamFilter->content     = $request->content;
        $spamFilter->save();
        $notify[] = ['success', $massage];
        return redirect()->back()->withNotify($notify);
    }

    public function delete($id)
    {
        $spamFilter = SpamFilter::findOrFail($id);
        $spamFilter->delete();
        $notify[] = ['success', 'Spam filter deleted successfully'];
        return redirect()->back()->withNotify($notify);
    }
}
