<h1>Specializations</h1>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
    </tr>

    <?php foreach ($specializations as $spec): ?>
        <tr>
            <td><?= $spec['id'] ?></td>
            <td><?= sanitize($spec['name']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>