@extends('layouts.app')

@section('scripts')
	<script type="text/javascript">
		$(document).ready( function () {
			fav = $('#favForm');
			


			fav.click( function (e) {
				e.preventDefault();

				action = $(this).attr('action');
				token = fav.children('#_token').val();
				data = {_token: token};

				$.post(action, data, function (d) {

					button = fav.children('.fav-button');
					id = button.attr('id');
					'<i class="fa fa-lg fa-heart"></i>'
			

					if (id == 'button-favorite') {
						replaceClass = 'button-unfavorite';
						replaceHTML = '<i class="fa fa-lg fa-heart"></i> Unlike';
					} else if (id == 'button-unfavorite') {
						replaceClass = 'button-favorite';
						replaceHTML = '<i class="fa fa-lg fa-heart-o"></i> Like';
					}

					
					actionArray = action.split('/');
					currentAction = actionArray[3];
					currentVideo = actionArray[2];
					var reverseAction;
					
					if (currentAction == 'favorite') {
						reverseAction = 'unfavorite';
					} else {
						reverseAction = 'favorite';
					}


					button.attr('id', function () {
						return 'button-' + reverseAction;
					});


					button.removeClass(id).addClass(replaceClass).html(replaceHTML);
					
					fav.attr('action', function () {
						return '/videos/' + currentVideo + '/' + reverseAction;
					});
				});

			});

			$('.edit-comment-form-openers').click( function () {

				id = $(this).attr('id');
				commentID = $(this).attr('comment');
				divID = $(this).attr('for');
				performToggle(id, divID, "edit-comment-forms");
				// $('.current-comment#current-comment-' + commentID).addClass('fadeOutRight animated').hide();
				$('.current-comment#current-comment-' + commentID).toggle();
			});
		});
	</script>
@endsection

@section('content')
	<div class="container">
	    <div class="row">
	    	<div class="col-md-9">
	    		<div id="video-screen">
	    			<iframe id="video-frame" src="{{ $video->srcFrame() }}?autoplay=0" frameborder="0" allowfullscreen ></iframe>
	    		</div>
	    		<div class="row">
	    			<div class="col-md-10">
			    		<div class="section-title text-center video-title">
			    			<h2>{{ $video->title }}</h2>
			    			<p></p>
				        </div>
	    			</div>
	    			@if(Auth::user())
						<div>
						    @if( Auth::user()->favors($video) )
						        @include('partials.fav', [
						        	'action' => 'unfavorite',
						        	'model' => 'videos',
						        	'id' => $video->id,
						        	'button' => 'Unlike',
						        ])
						    @else
						        @include('partials.fav', [
						        	'action' => 'favorite',
							        'model' => 'videos',
							        'id' => $video->id,
							        'button' => 'Like',
						       	])
						    @endif
						</div>
					@else
						<a href="{{ url('/login') }}"><button class="bolt-button button-half">Like this video</button></a>
					@endif
		        </div>
	    	</div>
	    	<div class="col-md-3 video-right">
			    	<form action="/videos/{{ $video->id }}/comments/add" method="POST">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<textarea name="comment" placeholder="Post a comment." maxlength="255" reqkuired>{{ old('comment') }}</textarea>
					      <span class="help-block">
                                <strong>{{ $errors->first('comment') }}</strong>
                            </span>

                    	<button class="bolt-button button-full" type="submit">POST</button>
					</form>

		    	<div class="row video-comments">

	    			@foreach($comments as $comment)
	    				<div class="col-md-12 single-comment">
	    					<div class="row single-comment-row">
	    						<div class="col-md-2 col-sm-3 commenter-avatar">
	    							<img class="img-responsive" src="{{ $comment->user->getAvatar() }}">
    								@if(Auth::user())
    									@if(Auth::user()->owns($comment))
		    								<button href="#" class="edit-comment-form-openers" comment="{{$comment->id}}" for="edit-comment-{{ $comment->id }}" id="open-edit-for-{{$comment->id}}"> Edit </button>
		    							@endif
    								@endif
	    						</div>
	    						<div class="col-md-10 col-sm-9 comment-body">
	    							<div class="row">
	    								<div class="col-md-12 current-comment" id="current-comment-{{$comment->id}}">
	    									<p class="comment-text">{{ $comment->comment }}</p>
		    								<p class="comment-info">{{ $comment->commentedAt() }} {{ $comment->is_edited() }}</p>
	    								</div>
	    								@if(Auth::user())
	    									@if(Auth::user()->owns($comment))
		    									<div class="col-md-12 edit-comment-forms fadeIn animated" hidden id="edit-comment-{{ $comment->id }}">
							    					<form action="/comments/{{$comment->id}}" method="POST">
														<input type="hidden" name="_token" value="{{ csrf_token() }}">
														{!! method_field('patch') !!}
														<textarea name="comment" id="comment" placeholder="Edit a comment." maxlength="255" required>{{ $comment->comment }}</textarea>
													      <span class="help-block">
								                                <strong>{{ $errors->first('comment') }}</strong>
								                            </span>
								                    	<button class="button-full" id="submit-comment-edit" type="submit">Update</button>
													</form>
							    				</div>
		    								@endif
	    								@endif
	    							</div>
	    						</div>
	    					</div>
	    				</div>
	    				
	    			@endforeach
		    	</div>
	    	</div>
	    </div>
	</div>
@endsection

<style type="text/css">
	#video-screen {
		position: relative;
		height: 0;
		/*padding-top: 25px;*/
		padding-bottom: 56.25%; /* 16:9 */
	}

	#video-screen iframe {
		position: absolute;
		/*top: 0;*/
		left: 0;
		right: 0;
		padding: 0 30px;
		width: 100%;
		height: 100%;
	}

	.video-comments {
		/*overflow: scroll;*/
		/*max-height: 70vh;*/
		background: rgb(221, 224, 224);
	    border: none;
	    padding: 0px;
	    border-radius: 3px;
	}

	.video-right {
		padding: 0;
	}

	.single-comment {
		border-bottom: rgba(204, 204, 204, 0.54) solid 2px;
    	padding: 3px;
	}

	.commenter-avatar {
		padding: 0;

	}

	.single-comment-row div {
		padding: 0 17px;
	}

	.comment-info {
		font-style: italic;
    	color: #8F0A0A;
    	font-size: 85%;
	}

	.comment-text {
		word-wrap: break-word;
	    text-align: justify;
	}

	iframe {
		position: absolute;
	}
</style>


<!-- <form action="/videos/{{ $video->id }}/comments/add" method="POST">
	<input type="_hidden" name="_token" value="{{ csrf_token() }}">
	<input type="text" name="comment" value="{{ old('comment') }}" maxlength="255" required>
	<button type="submit">POST</button>
</form>

@if(Auth::user())
<div>
    @if( Auth::user()->favors($video) )
        @include('partials.fav', ['action' => 'unfavorite', 'model' => 'videos', 'id' => $video->id, 'button' => 'Unfavorite'])
    @else
        @include('partials.fav', ['action' => 'favorite', 'model' => 'videos', 'id' => $video->id, 'button' => 'Favorite'])
    @endif
</div>
@endif

@foreach($comments as $comment)
{{ $comment->title }}
@endforeach -->