<?php
require('XLSXReader.php');
if(isset($_FILES['excel']['name']) && !isset($_POST['column']))
{
    $file = new XLSXReader($_FILES['excel']['tmp_name']);
    $response = json_encode($file->getSheetNames());
    die($response);
    
}
// 
if(isset($_POST['sheet']) && !isset($_POST['column']))
{
    $file = new XLSXReader($_FILES['choose_file']['tmp_name']);
    $getCol = $file->getSheetData($_POST['sheet'])[0];
    $response = json_encode($getCol);
    die($response);
}
// 
if(isset($_POST['column']))
{
    $file = new XLSXReader($_FILES['excel']['tmp_name']);
    $getCols = $file->getSheetData($_POST['sheet']);
    $output = [];
    foreach($getCols as $key => $val)
    {
        if($key != 0)
        {
            $output[] = $val[$_POST['column']];
        }
    }
    $response = json_encode($output);
    die($response);
}


?>

<form action="" method="post" enctype="multipart/form-data">
    <input id="fileImport" type="file" name="excel" id="">
    <br><br>
    <select name="sheet" id="selectSheet">
        <option value="">Please Input Excel File</option>
    </select>
    <br><br>
    <select name="column" id="selectColumn">
        <option value="">Please Sleect Sheet Name</option>
    </select>
    <br><br>
    <button name="submit" disabled>Submit</button>
</form>
<div id="output"></div>

<script  src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    (function($){
        var fileSrc = null;
        $('#fileImport').change(function(e){
            fileSrc = e.target.files[0];
            $('#selectSheet').html(`<option value="">Please Sleect Excel File</option>`);
            var formData = new FormData();
            formData.append('excel', fileSrc);
            $.ajax({
                url:'index.php',
                method:'post',
                contentType:false,
                processData:false,
                catch:false,
                data:formData,
                success:function(res)
                {
                    if(res)
                    {
                        var sheetList = JSON.parse(res);
                        var html = '<option value="-1">Select Sheet Name</option>';
                        Object.keys(sheetList).forEach((i)=>{
                            html += `<option value="${sheetList[i]}">${sheetList[i]}</option>`;
                        });
                        $('#selectSheet').html(html);
                    }
                }

            });
        });
        $('#selectSheet').change(function(e){
            var formData = new FormData();
            formData.append('choose_file', fileSrc);
            formData.append('sheet', $(this).val());
            $.ajax({
                url:'index.php',
                method:'post',
                contentType:false,
                processData:false,
                catch:false,
                data:formData,
                success:function(res)
                {
                    if(res.length > 0)
                    {
                        var sheetCols = JSON.parse(res);
                        var html = '<option value="-1">Select Column Name</option>';
                        sheetCols.forEach((col, i)=>{
                            html += `<option value="${i}">${col}</option>`;
                        });
                        $('#selectColumn').html(html);
                        $('button').removeAttr('disabled');
                    }
                }

            });
        });
        // 
        $('form').submit(function(e){
            e.preventDefault();
            $.ajax({
                url:'index.php',
                method:'post',
                contentType:false,
                processData:false,
                catch:false,
                data:new FormData(this),
                success:function(res)
                {
                    $('#output').html(res);
                }

            });
        });
    })(jQuery);
</script>