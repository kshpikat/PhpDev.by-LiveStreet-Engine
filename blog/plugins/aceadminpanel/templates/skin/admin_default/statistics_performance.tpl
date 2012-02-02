{if $bIsShowStatsPerformance AND $oUserCurrent AND $oUserCurrent->isAdministrator()}
    <div class="stat-performance">
        <h2>Statistics performance</h2>
        <table>
            <tr>
                <td>
                    <h4>MySql</h4>
                    query: <strong>{$aStatsPerformance.sql.count}</strong><br />
                    time: <strong>{$aStatsPerformance.sql.time}</strong>
                </td>
                <td>
                    <h4>Cache</h4>
                    query: <strong>{$aStatsPerformance.cache.count}</strong><br />
                    &mdash; set: <strong>{$aStatsPerformance.cache.count_set}</strong><br />
                    &mdash; get: <strong>{$aStatsPerformance.cache.count_get}</strong><br />
                    time: <strong>{$aStatsPerformance.cache.time}</strong>
                </td>
                <td>
                    <h4>PHP</h4>
                    time load modules: <strong>{$aStatsPerformance.engine.time_load_module}</strong><br />
                    full time: <strong>{$iTimeFullPerformance}</strong>
                </td>
                <td>
                    <h4>Memory</h4>
                    memory limit: <strong>{$aMemoryStats.memory_limit}</strong><br />
                    usage: <strong>{$aMemoryStats.usage}</strong><br />
                    peak usage: <strong>{$aMemoryStats.peak_usage}</strong><br />
                </td>
            </tr>
        </table>
    </div>
{/if}