<?php

namespace QuarkCMS\QuarkAdmin\Http\Requests;

class ResourceEditableRequest extends QuarkRequest
{
    /**
     * 执行行为
     *
     * @return array
     */
    public function handleRequest()
    {
        $data = $this->all();

        foreach ($data as $key => $value) {
            if($value === 'true' || $value === 'false') {
                $data[$key] = $value === 'true' ? 1 : 0;
            }
        }
        if (isset($data['s'])) {
            unset($data['s']);
        }
        return $this->model()->where('id', $this->input('id'))->update($data);
    }
}
