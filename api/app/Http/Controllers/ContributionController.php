<?php

namespace App\Http\Controllers;

use App\Rules\Contribution\HasPermissionInLan as HasPermissionInLanContribution;
use App\Rules\ContributionCategory\HasPermissionInLan as HasPermissionInLanContributionCategory;
use App\Rules\General\OneOfTwoFields;
use App\Rules\User\HasPermissionInLan;
use App\Services\Implementation\ContributionServiceImpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Validation et application de la logique applicative sur les contributions.
 *
 * Class ContributionController
 */
class ContributionController extends Controller
{
    /**
     * Service de contribution.
     *
     * @var ContributionServiceImpl
     */
    protected $contributionService;

    /**
     * ContributionController constructor.
     *
     * @param ContributionServiceImpl $contributionService
     */
    public function __construct(ContributionServiceImpl $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#contribution
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createCategory(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id'     => $request->input('lan_id'),
            'name'       => $request->input('name'),
            'permission' => 'create-contribution-category',
        ], [
            'lan_id'     => 'integer|exists:lan,id,deleted_at,NULL',
            'name'       => 'required|string',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->createCategory(
            $request->input('lan_id'),
            $request->input('name')
        ), 201);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#creer-une-contribution
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createContribution(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'contribution_category_id' => $request->input('contribution_category_id'),
            'user_full_name'           => $request->input('user_full_name'),
            'user_email'               => $request->input('user_email'),
            'permission'               => 'create-contribution',
        ], [
            'contribution_category_id' => 'required|integer|exists:contribution_category,id,deleted_at,NULL',
            'user_full_name'           => [
                'required_without:user_email',
                'string',
                'nullable',
                new OneOfTwoFields($request->input('user_email'), 'user_email'),
            ],
            'user_email' => [
                'required_without:user_full_name',
                'string',
                'nullable',
                'exists:user,email',
                new OneOfTwoFields($request->input('user_full_name'), 'user_full_name'),
            ],
            'permission' => new HasPermissionInLanContributionCategory(
                $request->input('contribution_category_id'),
                Auth::id()
            ),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->createContribution(
            $request->input('contribution_category_id'),
            $request->input('user_full_name'),
            $request->input('user_email')
        ), 201);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-une-categorie-de-contribution
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCategory(Request $request)
    {
        $validator = Validator::make([
            'contribution_category_id' => $request->input('contribution_category_id'),
            'permission'               => 'delete-contribution-category',
        ], [
            'contribution_category_id' => 'required|integer|exists:contribution_category,id,deleted_at,NULL',
            'permission'               => new HasPermissionInLanContributionCategory(
                $request->input('contribution_category_id'),
                Auth::id()
            ),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->deleteCategory(
            $request->input('contribution_category_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-une-contribution
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteContribution(Request $request)
    {
        $validator = Validator::make([
            'contribution_id' => $request->input('contribution_id'),
            'permission'      => 'delete-contribution',
        ], [
            'contribution_id' => 'required|integer|exists:contribution,id,deleted_at,NULL',
            'permission'      => new HasPermissionInLanContribution($request->input('contribution_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->deleteContribution(
            $request->input('contribution_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#lister-les-categories-de-contribution
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->getCategories(
            $request->input('lan_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#lister-les-contributions
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContributions(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->getContributions(
            $request->input('lan_id')
        ), 200);
    }
}
