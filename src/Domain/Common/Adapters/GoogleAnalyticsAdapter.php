<?php
/**
 * MIT License
 *
 * Copyright (c) 2018 PHP DLX
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Reservas\Domain\Common\Adapters;


use Google_Client;
use Google_Exception;
use Google_Service_Analytics;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_GetReportsResponse;
use Google_Service_AnalyticsReporting_ReportRequest;

class GoogleAnalyticsAdapter
{
    /**
     * @var Google_Service_AnalyticsReporting
     */
    private $analytics;
    /**
     * @var string
     */
    private $json_key;
    /**
     * @var string|null
     */
    private $nome_app;

    /**
     * AnalyticsAdapter constructor.
     * @param string $json_key
     * @param string|null $nome_app
     */
    public function __construct(string $json_key, ?string $nome_app = null)
    {
        $this->json_key = $json_key;
        $this->nome_app = $nome_app;
    }

    /**
     * @throws Google_Exception
     */
    public function conectar(): void
    {
        $google_client = new Google_Client();
        $google_client->setApplicationName($this->nome_app);
        $google_client->setAuthConfig($this->json_key);
        $google_client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);

        $this->analytics = new Google_Service_AnalyticsReporting($google_client);
    }

    /**
     * @param Google_Service_AnalyticsReporting_ReportRequest $google_request
     * @return Google_Service_AnalyticsReporting_GetReportsResponse
     */
    public function getReport(Google_Service_AnalyticsReporting_ReportRequest $google_request): Google_Service_AnalyticsReporting_GetReportsResponse
    {
        $report = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $report->setReportRequests([$google_request]);

        return $this->analytics->reports->batchGet($report);
    }

    public function printResults($reports)
    {
        for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
            $report = $reports[$reportIndex];
            $header = $report->getColumnHeader();
            $dimensionHeaders = $header->getDimensions();
            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
            $rows = $report->getData()->getRows();

            for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                $row = $rows[$rowIndex];
                $dimensions = $row->getDimensions();
                $metrics = $row->getMetrics();
                for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
                    print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
                }

                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        $entry = $metricHeaders[$k];
                        print($entry->getName() . ": " . $values[$k] . "\n");
                    }
                }
            }
        }
    }
}