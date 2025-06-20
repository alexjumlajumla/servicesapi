<?php
declare(strict_types=1);

namespace App\Services\FaqService;

use App\Helpers\ResponseError;
use App\Models\Faq;
use App\Services\CoreService;
use Illuminate\Support\Str;
use Throwable;

class FaqService extends CoreService
{
    protected function getModelClass(): string
    {
        return Faq::class;
    }

    public function create(array $data): array
    {
        try {
            $faq = $this->model()->create([
                'uuid' => Str::uuid(),
                'type' => 'web',
            ]);

            $this->setQuestions($faq, $data);

            return [
                'status'    => true,
                'code'      => ResponseError::NO_ERROR,
                'data'      => $faq
            ];
        } catch (Throwable $e) {
            return [
                'status'    => false,
                'code'      => ResponseError::ERROR_501,
                'message'   => $e->getMessage()
            ];
        }
    }

    public function update(string $uuid, array $data): array
    {
        try {
            $faq = Faq::where('uuid', $uuid)->first();

            if (empty($faq)) {
                return ['status' => false, 'code' => ResponseError::ERROR_404];
            }

            $faq->update(['type' => 'web']);

            $this->setQuestions($faq, $data);

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $faq->fresh('translations')];
        } catch (Throwable $e) {
            $this->error($e);
            return ['status' => false, 'code' => ResponseError::ERROR_502, 'message' => ResponseError::ERROR_502];
        }
    }

    public function setQuestions(Faq $faq, array $data): bool
    {
        if (!is_array(data_get($data, 'question'))) {
            return false;
        }

        if (!empty($faq->translations)) {
            $faq->translations()->delete();
        }

        foreach (data_get($data, 'question') as $index => $item) {

            $faq->translations()->create([
                'locale'    => $index,
                'question'  => $item,
                'answer'    => data_get($data, "answer.$index"),
            ]);

        }

        return true;
    }

    public function setStatus(string $uuid): array
    {
        $faq = $this->model()->firstWhere('uuid', $uuid);

        if (empty($faq)) {
            return ['status' => false, 'code' => ResponseError::ERROR_404];
        }

        /** @var Faq $faq */
        $faq->update(['active' => !$faq->active]);

        return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $faq];
    }
}
