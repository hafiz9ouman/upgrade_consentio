      <table class=""  id="report_table" >
        <thead>
          <tr style="background: rgb(122, 118, 118);    color: white;">
          <th>{{ __('Detail Data Inventory') }}  </th><th>{{ date("Y-m-d") }}</th>
          </tr>
        
        </thead>
        <tbody >
            @foreach($emails as $question)

            <tr style="background: rgb(15, 117, 185); color: white; " >
               <td ><strong>Question</strong></td>
               <td > <strong>{{ $question->user_email }}({{ $question->sub_form_name }}) </strong> {{ __('Responses') }}</td>
            </tr>
                @foreach($question->all_users_responses as $user_responses)
                   <?php  $question_name = DB::table('questions')->where('id' , $user_responses->question_id )->pluck('question')->first();
                          $user_responses_array = explode(",", $user_responses->question_response );   
                    ?>
                       <tr style="background: rgb(115, 184, 77);color: white;">
                          <th><strong>{{ ucfirst(strtolower(trim($question_name))) }}</strong></th>
                          <td></td>
                       </tr>
                        @foreach($user_responses_array as $res)
                            <tr style="border: 1px solid black;">
                                <td></td>
                                <td > {{ ucfirst(strtolower(trim($res))) }}</td>
                            </tr>
                        @endforeach      
                @endforeach
               
          @endforeach 
        </tbody>
      </table>		