<table style="width: 100%">
    @forelse (array_chunk($images, 2) as $row)
        <tr>
            @foreach ($row as $value)
                <td width="40%" style="text-align: center; padding:10px; background: #fff;">
                    <img src='{{ public_path("uploads/$value->imagen") }}' style="display:block; width: 87%; height:auto">
                </td>
            @endforeach
        </tr>
    @empty
        <tr>
            <td style="background: #fff;">
                <h3 style="text-align:center">No images</h3>
            </td>
        </tr>
    @endforelse
</table>