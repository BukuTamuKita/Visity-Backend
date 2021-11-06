<?php

namespace App\Exports;

use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ExportResource;
use App\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AppointmentExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $appointment = Appointment::all();
        return ExportResource::collection($appointment);
    }

    public function headings(): array
    {
        return ["id", "Hosts", "Guests", "Purpose", "Status", "Notes", "Date/Time"];
    }
}
