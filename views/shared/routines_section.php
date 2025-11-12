<?php
// Shared routines list section
// Expects: $role ('member'|'trainer') and $routines (array)
?>
<div class="routines-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3><i class='bx bx-list-ul'></i> My Workout Routines</h3>
        <button class="btn btn-primary" onclick="openCreateRoutineModal()">
            <i class='bx bx-plus'></i> New Routine
        </button>
    </div>
    <div class="routine-list">
        <?php if (!empty($routines)): ?>
            <?php foreach ($routines as $routine): ?>
                <div class="routine-item">
                    <div class="routine-header">
                        <div class="routine-info">
                            <h4><?= htmlspecialchars($routine['name']) ?></h4>
                            <p>
                                <?= date('M j, Y', strtotime($routine['created_at'])) ?>
                                <?php if (!empty($routine['is_public'])): ?>
                                    • <span style="color: #10b981;">Public</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="routine-actions">
                            <button class="btn btn-sm btn-primary" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;padding:0;" onclick="viewRoutine(<?= (int)$routine['id'] ?>)">
                                <i class='bx bx-show'></i>
                            </button>
                            <button class="btn btn-sm btn-secondary" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;padding:0;" onclick="editRoutine(<?= (int)$routine['id'] ?>)">
                                <i class='bx bx-edit'></i>
                            </button>
                            <button class="btn btn-sm btn-danger" style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;padding:0;" onclick="deleteRoutine(<?= (int)$routine['id'] ?>)">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-data">
                <i class='bx bx-list-ul'></i>
                <p>No routines created yet. Create your first routine!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
