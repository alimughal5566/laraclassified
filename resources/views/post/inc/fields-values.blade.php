@if (isset($customFields) and $customFields->count() > 0)
	<div class="row" id="cfContainer">
		<div class="col-xl-12">
			<div class="row pl-2 pr-2">
				<div class="col-xl-12 pb-2 pl-1 pr-1">
					<h4><i class="icon-th-list"></i> {{ t('Additional Details') }}</h4>
				</div>
			</div>
		</div>
		
		<div class="col-xl-12">
			<div class="row pl-2 pr-2">
				@foreach($customFields as $field)
					<?php
					if (in_array($field->type, ['radio', 'select'])) {
						if (is_numeric($field->default_value)) {
							$option = \App\Models\FieldOption::find($field->default_value);
							if (!empty($option)) {
								$field->default_value = $option->value;
							}
						}
					}
					if (in_array($field->type, ['checkbox'])) {
						$field->default_value = ($field->default_value == 1) ? t('Yes') : t('No');
					}
					if ($field->type == 'video') {
						$field->default_value = \App\Helpers\VideoEmbedding::embedVideo($field->default_value);
					}
					?>
					@if ($field->type == 'file')
						<div class="detail-line col-xl-12 pb-2 pl-1 pr-1">
							<div class="rounded-small ml-0 mr-0 p-2">
								<span class="detail-line-label" style="padding-top: 8px;">{{ $field->name }}</span>
								<span class="detail-line-value">
									<a class="btn btn-default" href="{{ fileUrl($field->default_value) }}" target="_blank">
										<i class="icon-attach-2"></i> {{ t('Download') }}
									</a>
								</span>
							</div>
						</div>
					@else
						@if (!is_array($field->default_value) and $field->type != 'video')
							@if ($field->type == 'url')
								<div class="detail-line col-sm-6 col-xs-12 pb-2 pl-1 pr-1">
									<div class="rounded-small p-2">
										<span class="detail-line-label">{{ $field->name }}</span>
										<span class="detail-line-value">
											<a href="{{ addHttp($field->default_value) }}" target="_blank" rel="nofollow">{{ addHttp($field->default_value) }}</a>
										</span>
									</div>
								</div>
							@else
								<div class="detail-line col-sm-6 col-xs-12 pb-2 pl-1 pr-1">
									<div class="rounded-small p-2">
										<span class="detail-line-label">{{ $field->name }}</span>
										<span class="detail-line-value">{{ $field->default_value }}</span>
									</div>
								</div>
							@endif
						@elseif (!is_array($field->default_value) and $field->type == 'video')
							<div class="detail-line col-xl-12 pb-2 pl-1 pr-1">
								<div class="rounded-small p-2">
									<span>{{ $field->name }}:</span>
									<div class="row m-0 p-2">
										<div class="col-lg-12 col-md-12 col-sm-12 text-center embed-responsive embed-responsive-16by9">
											{!! $field->default_value !!}
										</div>
									</div>
								</div>
							</div>
						@else
							@if (count($field->default_value) > 0)
							<div class="detail-line col-xl-12 pb-2 pl-1 pr-1">
								<div class="rounded-small p-2">
									<span>{{ $field->name }}:</span>
									<div class="row m-0 p-2">
										@foreach($field->default_value as $valueItem)
											@continue(!isset($valueItem->value))
											<div class="col-sm-4 col-xs-6 col-xxs-12">
												<div class="m-0">
													<i class="fa fa-check"></i> {{ $valueItem->value }}
												</div>
											</div>
										@endforeach
									</div>
								</div>
							</div>
							@endif
						@endif
					@endif
				@endforeach
			</div>
		</div>
	</div>
@endif
