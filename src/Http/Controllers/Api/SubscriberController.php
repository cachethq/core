<?php

namespace Cachet\Http\Controllers\Api;

use Cachet\Actions\Subscriber\CreateSubscriber;
use Cachet\Actions\Subscriber\UnsubscribeSubscriber;
use Cachet\Actions\Subscriber\UpdateSubscriber;
use Cachet\Concerns\GuardsApiAbilities;
use Cachet\Data\Requests\Subscriber\CreateSubscriberRequestData;
use Cachet\Data\Requests\Subscriber\UpdateSubscriberRequestData;
use Cachet\Filters\MetaFilter;
use Cachet\Http\Resources\Subscriber as SubscriberResource;
use Cachet\Models\Subscriber;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Number;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

#[Group('Subscribers', weight: 10)]
class SubscriberController extends Controller
{
    use GuardsApiAbilities;

    /**
     * List Subscribers
     */
    #[QueryParameter('filter[email]', 'Filter by email address.', example: 'john@example.com')]
    #[QueryParameter('filter[global]', 'Filter by global subscription status.', type: 'bool', example: '1')]
    #[QueryParameter('filter[meta][key]', 'Filter by a metadata key/value pair.', example: 'eu-west')]
    #[QueryParameter('include', 'Include related data (components, meta).', example: 'meta')]
    #[QueryParameter('per_page', 'How many items to show per page.', type: 'int', default: 15, example: 20)]
    #[QueryParameter('page', 'Which page to show.', type: 'int', example: 2)]
    public function index(Request $request)
    {
        $this->guard('subscribers.manage');

        $subscribers = QueryBuilder::for(Subscriber::class)
            ->allowedIncludes(['components', 'meta'])
            ->allowedFilters([
                'email',
                AllowedFilter::exact('global'),
                AllowedFilter::custom('meta', new MetaFilter),
            ])
            ->allowedSorts(['email', 'id'])
            ->simplePaginate(Number::clamp($request->integer('per_page', 15), min: 1, max: 100));

        return SubscriberResource::collection($subscribers);
    }

    /**
     * Create Subscriber
     */
    public function store(CreateSubscriberRequestData $data, CreateSubscriber $createSubscriberAction)
    {
        $this->guard('subscribers.manage');

        $subscriber = $createSubscriberAction->handle(
            $data->email,
            $data->global ?? true,
            $data->components ?? [],
            $data->verified ?? false,
            $data->meta,
        );

        if (! $subscriber->hasVerifiedEmail()) {
            $subscriber->sendEmailVerificationNotification();
        }

        return SubscriberResource::make($subscriber);
    }

    /**
     * Get Subscriber
     */
    #[QueryParameter('include', 'Include related data (components, meta).', example: 'meta')]
    public function show(Subscriber $subscriber)
    {
        $this->guard('subscribers.manage');

        $subscriberQuery = QueryBuilder::for(Subscriber::class)
            ->allowedIncludes(['components', 'meta'])
            ->find($subscriber->id);

        return SubscriberResource::make($subscriberQuery)
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update Subscriber
     */
    public function update(UpdateSubscriberRequestData $data, Subscriber $subscriber, UpdateSubscriber $updateSubscriberAction)
    {
        $this->guard('subscribers.manage');

        $updateSubscriberAction->handle(
            $subscriber,
            email: $data->email,
            global: $data->global,
            components: $data->components,
            meta: $data->meta,
        );

        return SubscriberResource::make($subscriber->fresh());
    }

    /**
     * Delete Subscriber
     */
    public function destroy(Subscriber $subscriber, UnsubscribeSubscriber $unsubscribeSubscriberAction)
    {
        $this->guard('subscribers.delete');

        $unsubscribeSubscriberAction->handle($subscriber);

        return response()->noContent();
    }
}
