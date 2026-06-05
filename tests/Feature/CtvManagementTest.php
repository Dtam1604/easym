<?php

namespace Tests\Feature;

use App\Models\NguoiDung;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CtvManagementTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test Admin can view the CTV management page.
     */
    public function test_admin_can_view_ctv_management_page(): void
    {
        $admin = NguoiDung::where('vai_tro', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('admin.ctv.index'));
        $response->assertStatus(200);
        $response->assertSee('Quản lý Cộng tác viên');
    }

    /**
     * Test non-admin cannot view the CTV management page.
     */
    public function test_non_admin_cannot_view_ctv_management_page(): void
    {
        $user = NguoiDung::where('vai_tro', 'nguoi_tim_tro')->first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get(route('admin.ctv.index'));
        $response->assertStatus(403);
    }

    /**
     * Test Admin can create a new CTV account.
     */
    public function test_admin_can_create_ctv_account(): void
    {
        $admin = NguoiDung::where('vai_tro', 'admin')->first();

        $response = $this->actingAs($admin)->post(route('admin.ctv.store'), [
            'ho_ten' => 'CTV Unit Test',
            'email' => 'ctvunittest@easym.vn',
            'so_dien_thoai' => '0999000111',
            'dia_ban_quan_ly' => 'Khu vực Xuân Mai',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('nguoi_dung', [
            'email' => 'ctvunittest@easym.vn',
            'vai_tro' => 'cong_tac_vien',
            'dia_ban_quan_ly' => 'Khu vực Xuân Mai',
            'trang_thai_khoa' => false,
        ]);
    }

    /**
     * Test Admin can lock and unlock a CTV account.
     */
    public function test_admin_can_lock_and_unlock_ctv_account(): void
    {
        $admin = NguoiDung::where('vai_tro', 'admin')->first();
        
        $ctv = NguoiDung::create([
            'ho_ten' => 'CTV To Lock',
            'email' => 'ctvtolock@easym.vn',
            'so_dien_thoai' => '0999000222',
            'dia_ban_quan_ly' => 'Hà Nội',
            'vai_tro' => 'cong_tac_vien',
            'mat_khau' => bcrypt('123456'),
            'trang_thai_khoa' => false,
        ]);

        // Toggle lock -> true
        $response = $this->actingAs($admin)->post(route('admin.ctv.toggle_lock', $ctv->id));
        $response->assertRedirect();
        $this->assertTrue($ctv->refresh()->trang_thai_khoa);

        // Toggle lock -> false
        $response = $this->actingAs($admin)->post(route('admin.ctv.toggle_lock', $ctv->id));
        $response->assertRedirect();
        $this->assertFalse($ctv->refresh()->trang_thai_khoa);
    }

    /**
     * Test locked CTV cannot log in.
     */
    public function test_locked_ctv_cannot_log_in(): void
    {
        $ctv = NguoiDung::create([
            'ho_ten' => 'CTV Locked',
            'email' => 'ctvlocked@easym.vn',
            'so_dien_thoai' => '0999000333',
            'vai_tro' => 'cong_tac_vien',
            'mat_khau' => bcrypt('123456'),
            'trang_thai_khoa' => true,
        ]);

        $response = $this->post('/dang-nhap', [
            'email' => 'ctvlocked@easym.vn',
            'mat_khau' => '123456',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(\Auth::check());
    }

    /**
     * Test Admin can update CTV region.
     */
    public function test_admin_can_update_ctv_region(): void
    {
        $admin = NguoiDung::where('vai_tro', 'admin')->first();
        
        $ctv = NguoiDung::create([
            'ho_ten' => 'CTV Region Test',
            'email' => 'ctvregion@easym.vn',
            'so_dien_thoai' => '0999000444',
            'vai_tro' => 'cong_tac_vien',
            'mat_khau' => bcrypt('123456'),
            'trang_thai_khoa' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.ctv.update_region', $ctv->id), [
            'dia_ban_quan_ly' => 'Hòa Lạc, Thạch Thất',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Hòa Lạc, Thạch Thất', $ctv->refresh()->dia_ban_quan_ly);
    }
}
