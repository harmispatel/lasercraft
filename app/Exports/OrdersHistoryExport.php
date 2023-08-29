<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class OrdersHistoryExport implements FromCollection, WithHeadings, WithEvents
{
    protected $orders;
    protected $shop_id;
    protected $sum_line_count;
    protected $total=0;

    public function __construct($orders,$shop_id)
    {
        $this->orders = $orders;
        $this->shop_id = $shop_id;
        $this->sum_line_count = count($this->orders) + 1;
    }

    public function collection()
    {
        $all_excel_data = [];

        foreach ($this->orders as $key => $order)
        {
            $firstname = (isset($order->firstname)) ? $order->firstname : '';
            $lastname = (isset($order->lastname)) ? $order->lastname : '';
            $customer = "$firstname $lastname";
            $chekout_type = (isset($order->checkout_type)) ? $order->checkout_type : '';
            $chekout_type = str_replace('_', ' ',$chekout_type);
            $chekout_type = ucfirst($chekout_type);

            $order_data = [];
            $order_data[] = $order->id;
            $order_data[] = ucfirst($order->order_status);
            $order_data[] = $order->customer;
            $order_data[] = (isset($order->phone)) ? $order->phone : '';
            $order_data[] = $chekout_type;
            $order_data[] = (isset($order->payment_method)) ? ucfirst($order->payment_method) : '';
            $order_data[] = date('d-m-Y h:i:s a',strtotime($order->created_at));
            $order_data[] = $order->order_total;

            $this->total += $order->order_total;

            $all_excel_data[] = $order_data;
        }

        return collect($all_excel_data);
    }

    // Sheet Heading
    public function headings(): array
    {
        $heading_arr = [];
        $heading_arr[] = 'Order No.';
        $heading_arr[] = 'Status';
        $heading_arr[] = 'Customer';
        $heading_arr[] = 'Mobile No.';
        $heading_arr[] = 'Checkout Type.';
        $heading_arr[] = 'Payment Method.';
        $heading_arr[] = 'Order Date';
        $heading_arr[] = 'Total Price';
        return $heading_arr;
    }

    // Sheets Settings
    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event)
            {
                // Set Cell width
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(17);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(17);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(18);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(17);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(25);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(22);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(17);

                // Set Background Color
                $event->sheet->getDelegate()->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('ECF0F1');

                // Set Border for Header
                $event->sheet->getDelegate()->getStyle('A1:H1')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ]);

                // Text Alignment
                $event->sheet->getDelegate()->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Set Bold font of Header
                $event->sheet->getDelegate()->getStyle('A1:H1')->getFont()->setBold(true)->setSize(14);

                // Add cell with SUM formula to last row
                $lineWhereputSum = $this->sum_line_count + 4;
                // $event->sheet->setCellValue("H{$lineWhereputSum}", "=SUM(H2:H{$this->sum_line_count})");
                $event->sheet->setCellValue("H{$lineWhereputSum}", "$this->total");
                $event->sheet->setCellValue("G{$lineWhereputSum}", "Total Amount");
                $event->sheet->getDelegate()->getStyle("G{$lineWhereputSum}:H{$lineWhereputSum}")->getFont()->setBold(true)->setSize(13);

                // Set Border for Total
                $event->sheet->getDelegate()->getStyle("G{$lineWhereputSum}:H{$lineWhereputSum}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ]
                ]);

            },
        ];
    }

}
