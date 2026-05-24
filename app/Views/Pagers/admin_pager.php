<?php $pager->setSurroundCount(2) ?>
<nav aria-label="Paginación de resultados" class="mt-4 d-flex justify-content-center">
    <ul class="pagination pagination-sm gap-2 mb-0">
    <?php if ($pager->hasPrevious()) : ?>
        <li class="page-item">
            <a class="page-link shadow-sm rounded border text-cva-brown bg-light" href="<?= $pager->getFirst() ?>" aria-label="Primera">
                <span aria-hidden="true"><i class="bi bi-chevron-double-left"></i></span>
            </a>
        </li>
        <li class="page-item">
            <a class="page-link shadow-sm rounded border text-cva-brown bg-light" href="<?= $pager->getPrevious() ?>" aria-label="Anterior">
                <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
            </a>
        </li>
    <?php endif ?>

    <?php foreach ($pager->links() as $link) : ?>
        <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
            <a class="page-link shadow-sm rounded border fw-bold <?= $link['active'] ? 'bg-brown text-gold border-gold' : 'text-cva-brown bg-light' ?>" href="<?= $link['uri'] ?>">
                <?= $link['title'] ?>
            </a>
        </li>
    <?php endforeach ?>

    <?php if ($pager->hasNext()) : ?>
        <li class="page-item">
            <a class="page-link shadow-sm rounded border text-cva-brown bg-light" href="<?= $pager->getNext() ?>" aria-label="Siguiente">
                <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
            </a>
        </li>
        <li class="page-item">
            <a class="page-link shadow-sm rounded border text-cva-brown bg-light" href="<?= $pager->getLast() ?>" aria-label="Última">
                <span aria-hidden="true"><i class="bi bi-chevron-double-right"></i></span>
            </a>
        </li>
    <?php endif ?>
    </ul>
</nav>
