<?php

namespace App\Http\Controllers;

use App\Exports\OutletExport;
use App\Exports\TemplateOutletExport;
use App\Models\Outlet;
use App\Http\Requests\StoreOutletRequest;
use App\Http\Requests\UpdateOutletRequest;
use App\Imports\OutletImport;
use App\Models\LogDB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        LogDB::record(Auth::user(), 'Mengakses ke view outlet', 'view outlet');

        return view('outlet.index', [
            'outlet' => Outlet::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOutletRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOutletRequest $request)
    {
        LogDB::record(Auth::user(), 'Insert data outlet', 'view outlet');

        $data = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'tlp' => 'required'
        ]);
        $outlet = Outlet::create($data);

        if ( $outlet ) {
            return redirect()->route('outlet.index')->with('success', 'Data outlet telah berhasil ditambahkan.');
        } else {
            return redirect()->route('outlet.index')->with('error', 'Data outlet gagal ditambahkan!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function show(Outlet $outlet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function edit(Outlet $outlet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOutletRequest  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOutletRequest $request, $id)
    {
        LogDB::record(Auth::user(), 'Update data outlet', 'view outlet');

        $outlet = Outlet::findOrFail($id);

        $rules = $request->validate([
            'nama' => 'required',
            'alamat' => 'required',
            'tlp' => 'required',
        ]);

        $update = $outlet->update($rules);

        if ( $update ) {
            return redirect()->route('outlet.index')->with('success', 'Data outlet telah berhasil di-edit');
        } else {
            return redirect()->route('outlet.index')->with('error', 'Data outlet gagal di-edit');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        LogDB::record(Auth::user(), 'Delete data outlet', 'view outlet');

        $outlet = Outlet::findOrFail($id);

        $delete = $outlet->delete($id);

        if ( $delete ) {
            return redirect()->route('outlet.index')->with('success', 'Data outlet tersebut telah berhasil dihapus');
        } else {
            return redirect()->route('outlet.index')->with('error', 'Data outlet tersebut gagal dihapus!');
        }
    }

    public function export() {
        LogDB::record(Auth::user(), 'Excel export data outlet', 'view outlet');

        $date = date('Y-m-d');
        return Excel::download(new OutletExport, $date . '_outlet.xlsx');
    }

    public function import(Request $request) {
        LogDB::record(Auth::user(), 'Excel import data outlet', 'view outlet');

        $validatedData = $request->validate([
            'excel' => 'required|file|mimes:xlsx,csv,xls'
        ]);

        if ( $validatedData ) {
            Excel::import(new OutletImport, $request->file('excel'));
        } else {
            return back()->withErrors([
                'excel' => 'File excel tidak terisi atau ada kesalahan!',
            ]);
        }

        return redirect()->route('outlet.index')->with('success', 'File excel telah diimport!');
    }

    public function exportTemplate()
    {
        LogDB::record(Auth::user(), 'Excel export template data outlet', 'view outlet');

        return Excel::download(new TemplateOutletExport, 'outlet_template.xlsx');
    }

    // make a function for generate outlet PDF using DOMPDF
    public function downloadPDF()
    {
        LogDB::record(Auth::user(), 'Generate PDF data outlet', 'view outlet');

        $outlet = Outlet::all();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('outlet.download-pdf', ['outlet' => $outlet]);
        return $pdf->download('outlet.pdf');
    }

}
