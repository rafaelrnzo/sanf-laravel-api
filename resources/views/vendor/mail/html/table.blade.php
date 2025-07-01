<div class="content-block">
    <table class="table-content-view" role="presentation" cellspacing="0" cellpadding="15" width="100%">
        @if(count($tableHead) > 0)
        <thead>
            <tr align="left">
                @foreach($tableHead as $th)
                    <th>{{ is_array($th) ? $th['label'] : $th }}</th>
                @endforeach
            </tr>
        </thead>
        @endif

        @if(count($tableBody) > 0)
        <tbody>
            @for($i = 0; $i < count($tableBody); $i++)
            <tr align="left">
                @foreach($tableHead as $target)
                    <td>{{ is_array($target) ? $tableBody[$i][$target['targetData']] : $tableBody[$i][$target] }}</td>
                @endforeach
            </tr>
            @endfor
        </tbody>
        @endif
    </table>
</div>
