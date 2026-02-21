<?php if (isset($pagination) && $pagination['total_pages'] > 1): ?>
    <?php
    $queryParams = $pagination['query_params'] ?? [];
    $baseQuery = http_build_query($queryParams);
    $sep = $baseQuery === '' ? '' : '&';
    ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($pagination['current_page'] > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= $baseQuery . $sep ?>page=<?= $pagination['current_page'] - 1 ?>">Previous</a>
                </li>
            <?php endif; ?>

            <?php
            $start = max(1, $pagination['current_page'] - 2);
            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);

            for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= $baseQuery . $sep ?>page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?= $baseQuery . $sep ?>page=<?= $pagination['current_page'] + 1 ?>">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

