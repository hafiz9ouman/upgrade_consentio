<?php 
         
          $array_index=0;
          // dd($total_count , $option_questions);
if(is_array($option_questions) && sizeof($option_questions)!=0){
?>
 @foreach($option_questions as $key=>$row)
 <table class="table table-striped border-0 detail_inv_page">
                <thead>
                  <th>{{ __('Question') }}</th>
                  <th>{{ __('Responses') }}</th>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="2">
                     @if(session('locale') == 'fr') 
                          @if(DB::table('questions')->where('question' , $row['question_string'])->pluck('question_fr')->first() != null)
                          {{ ucfirst(strtolower(trim(DB::table('questions')->where('question' , $row['question_string'])->pluck('question_fr')->first()))) }}
                          @else
                          {{ucfirst(strtolower(trim($row['question_string']))) }}
                          @endif

                       @else 
                       {{ ucfirst(strtolower(trim($row['question_string']))) }}
                     @endif
                  
                </td>
                <td></td>
              </tr>
                <?php 
                 if($key == 0){ 
                  $x = 0;
                 }
                
                  for($i = 0 ; $i <$option_questions[$key]['op_count']; $i++) { ?>
                    <tr>
                      <td class="check_icon text-right"><i class='bx bx-check'></i></td>
                      <td class="green_td_glb">
                      @if(session('locale') == 'fr')   
                      {{ucfirst(strtolower(trim($final_fr[$array_index])))}}
                      @else
                      {{ucfirst(strtolower(trim($final[$array_index])))}}
                      @endif
                    </td>
                      
                    </tr>

                    <?php 
                         $array_index++;              
                  }

                    
                    ?>

                </tbody>                
              </table>
@endforeach
<?php

}else{
?>
<table class="table table-striped border-0 detail_inv_page">

</table>

<?php

}

    ?>
