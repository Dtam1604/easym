# TÀI LIỆU CHI TIẾT VỀ CÁC THUẬT TOÁN VÀ CÔNG THỨC TOÁN HỌC (DÙNG CHO LUẬN VĂN/KỶ YẾU)

Tài liệu này giải thích chi tiết và trực quan hóa các công thức toán học đã được định nghĩa trong tài liệu **`Chuong_1_Co_so_ly_thuyet.md`** để phục vụ việc giải trình và bảo vệ khóa luận tốt nghiệp.

---

## 1. THUẬT TOÁN SO KHỚP BẠN Ở GHÉP THÍCH ỨNG

### 1.1. Mục tiêu
Đánh giá độ phù hợp lối sống (phần trăm tương đồng $P_{match}$ từ $0\%$ đến $100\%$) giữa **Người dùng chủ thể $A$** và **Ứng viên $B$** dựa trên khảo sát lối sống (`khao_sat_loi_song` lưu dưới dạng JSONB) và hệ thống trọng số (`trong_so_thuat_toan`).

---

### 1.2. Các Ký Hiệu Toán Học Và Giải Thích (Đồng bộ với Chương 1)

Dưới đây là bảng đối chiếu ký hiệu toán học trong công thức với thực tế triển khai trong mã nguồn:

| Ký hiệu | Ý nghĩa | Triển khai thực tế |
| :--- | :--- | :--- |
| $A$ | Người dùng chủ thể (chủ động tìm kiếm) | Người dùng đang đăng nhập hệ thống |
| $B$ | Người dùng ứng viên | Đối tượng ở ghép tiềm năng được duyệt qua |
| $C_{answered}$ | Tập hợp các tiêu chí mà cả $A$ và $B$ đều đã trả lời | Các câu hỏi lối sống không bị bỏ trống trong DB |
| $i$ | Một tiêu chí cụ thể thuộc tập hợp tiêu chí | Ví dụ: `gio_giac`, `do_sach_se`, `hut_thuoc` |
| $Val_{A, i}$ | Giá trị câu trả lời của $A$ đối với tiêu chí $i$ | `loiSongA['do_sach_se'] = 4` |
| $Val_{B, i}$ | Giá trị câu trả lời của $B$ đối với tiêu chí $i$ | `loiSongB['do_sach_se'] = 3` |
| $W_{base, i}$ | Trọng số nền (Base Weight) của tiêu chí $i$ | `trong_so_nen` trong database (ví dụ: `3.0` cho hút thuốc) |
| $P_{boost, i}$ | Hệ số nhân ưu tiên của tiêu chí $i$ | `he_so_uu_tien` trong database (ví dụ: `2.5` cho hút thuốc) |
| $U_i$ | Biến chỉ thị ưu tiên cá nhân của $A$ đối với tiêu chí $i$ | Nhận giá trị $1$ nếu tiêu chí $i$ nằm trong mảng `uu_tien` của $A$, ngược lại nhận $0$ |
| $W_{hybrid, i}$ | Trọng số lai của tiêu chí $i$ đối với người dùng $A$ | Trọng số tối đa khả thi của tiêu chí $i$ sau khi áp dụng ưu tiên |
| $M_i$ | Hệ số khớp thói quen đối với tiêu chí $i$ | Nhận giá trị từ $0.0$ đến $1.0$ tùy vào độ khớp câu trả lời |
| $P_{match}$ | Điểm phần trăm tương đồng lối sống tổng thể | Chỉ số phần trăm tương thích hiển thị trên UI (ví dụ: $85.5\%$) |
| $F(A, B)$ | Trạng thái lọc cứng (Bộ lọc cứng thích ứng) | Nhận giá trị $1$ (hợp lệ) hoặc $0$ (bị loại bỏ) |

---

### 1.2.1. Giải Nghĩa Chi Tiết Các Khái Niệm Cốt Lõi

#### 1. Thang điểm từ 1 đến 5 (1-5 Likert Scale) là gì?
* **Định nghĩa:** Là dạng thang đo tuyến tính biểu thị các cấp độ thái độ, thói quen hoặc hành vi tăng dần (từ rất thấp/rất ít đến rất cao/rất nhiều). Trong giao diện khảo sát lối sống, người dùng không chọn các câu trả lời dạng phân loại rời rạc (như Đúng/Sai) mà chọn các mức độ từ 1 đến 5 để biểu thị tần suất hoặc mức độ nghiêm ngặt của thói quen đó.
* **Ví dụ trong hệ thống:** 
  * Tiêu chí **`do_sach_se` (Mức độ sạch sẽ, ngăn nắp)**:
    * Mức 1: Rất bừa bộn (hoàn toàn không quan tâm dọn dẹp).
    * Mức 2: Ít dọn dẹp (chỉ dọn khi quá bẩn).
    * Mức 3: Bình thường (dọn dẹp định kỳ hàng tuần).
    * Mức 4: Sạch sẽ, gọn gàng (dọn dẹp hàng ngày).
    * Mức 5: Cực kỳ kỹ tính (yêu cầu vô trùng, không chấp nhận bừa bộn dù chỉ một chút).
* **Ý nghĩa thuật toán:** Giúp lượng hóa các thuộc tính định tính (tính cách, thói quen) thành giá trị số để thực hiện tính toán khoảng cách chênh lệch tuyệt đối: $|Val_{A, i} - Val_{B, i}|$. Nhờ vậy, hệ thống có thể áp dụng **Cơ chế nới lỏng thích ứng**: nếu chênh lệch chỉ là 1 bậc (ví dụ một người chọn mức 4 và người kia chọn mức 3), hệ thống vẫn ghi nhận sự tương đồng bán phần và cộng $50\%$ số điểm thành phần, thay vì cho ngay $0$ điểm như đối với các câu hỏi phân loại rời rạc.

#### 2. Trọng số nền (Base Weight - $W_{base, i}$) là gì?
* **Định nghĩa:** Là giá trị điểm số mặc định thể hiện **mức độ ảnh hưởng/tầm quan trọng khách quan** của một tiêu chí lối sống đối với tính hòa hợp khi sống chung dưới góc nhìn chung của cộng đồng. Trọng số này được thiết lập ban đầu bởi Quản trị viên hệ thống (Administrator) trong cơ sở dữ liệu (`trong_so_thuat_toan.trong_so_nen`).
* **Ví dụ trong hệ thống:**
  * Tiêu chí `hut_thuoc` (Mức độ hút thuốc) có trọng số nền rất cao là **`3.0`** vì đây là thói quen cực kỳ nhạy cảm, dễ gây ảnh hưởng trực tiếp đến sức khỏe và dẫn tới xung đột nghiêm trọng nhất.
  * Tiêu chí `nuoi_thu_cung` (Nuôi thú cưng) chỉ có trọng số nền là **`1.0`** vì mức độ ảnh hưởng của nó nhẹ nhàng hơn đối với đại đa số mọi người.
* **Ý nghĩa thuật toán:** Tạo ra sự phân cấp tầm quan trọng mặc định giữa các câu hỏi khảo sát. Khi người dùng $A$ điền khảo sát bình thường mà không đánh dấu tiêu chí đó là ưu tiên đặc biệt, hệ thống sẽ sử dụng Trọng số nền làm hệ số tính điểm cho câu hỏi đó.

#### 3. Hệ số nhân ưu tiên (Priority Multiplier - $P_{boost, i}$) là gì?
* **Định nghĩa:** Là hệ số nhân dùng để **khuếch đại (tăng trọng lượng) điểm số** của một tiêu chí cụ thể khi người dùng chủ thể $A$ đánh dấu tiêu chí đó là ưu tiên cá nhân đặc biệt quan trọng đối với họ (`uu_tien`). Giá trị này được lưu tại cột `he_so_uu_tien` trong bảng cấu hình trọng số.
* **Ví dụ trong hệ thống:** 
  * Tiêu chí `gio_giac` có trọng số nền $W_{base, i} = 2.0$ và hệ số ưu tiên $P_{boost, i} = 2.0$.
  * Nếu người dùng $A$ **không** chọn `gio_giac` vào danh sách ưu tiên cá nhân của mình ($U_i = 0$), trọng số lai $W_{hybrid, i}$ của câu hỏi này đối với $A$ chỉ là $2.0$.
  * Nếu người dùng $A$ **chọn** `gio_giac` là một tiêu chí ưu tiên hàng đầu ($U_i = 1$), trọng số lai $W_{hybrid, i}$ của câu hỏi này sẽ được khuếch đại lên thành: $2.0 \times 2.0 = 4.0$.
* **Ý nghĩa thuật toán:** Giúp thuật toán mang tính **thích ứng cá nhân hóa (Personalization Adaptive)**. Thay vì áp dụng một bộ trọng số cố định cho tất cả mọi người, thuật toán tự động điều chỉnh tầm quan trọng của từng câu hỏi dựa trên mong muốn thực tế của từng cá nhân. Một người cực kỳ ghét khói thuốc sẽ có trọng số câu hỏi hút thuốc cao hơn hẳn người không quá bận tâm về vấn đề này.

---

### 1.3. Mô Hình Toán Học Của Thuật Toán

Quá trình so khớp được chia làm 2 giai đoạn: **Lọc cứng thích ứng (Hard Filters)** và **Tính điểm tương đồng trọng số (Soft Matching)**.

#### Giai đoạn 1: Bộ lọc cứng thích ứng (Adaptive Constraints)
Trước khi tính điểm chi tiết, hệ thống áp dụng các ràng buộc để loại bỏ ngay lập tức các ứng viên hoàn toàn không phù hợp. Nếu vi phạm bất kỳ bộ lọc cứng nào dưới đây, điểm so khớp $P_{match}$ sẽ lập tức bằng $0\%$.

1. **Ràng buộc về Giới tính ($F_{gender}$):** Chỉ gợi ý những người cùng giới tính (hoặc trừ trường hợp một trong hai bên chọn giới tính là "khác" hoặc bỏ trống).
   $$F_{gender}(A, B) = \begin{cases} 
     0 & \text{nếu } UserA_{gender} \neq UserB_{gender} \text{ và cả hai không chọn "Khác"} \\ 
     1 & \text{nếu ngược lại} 
   \end{cases}$$

2. **Ràng buộc về Tôn giáo ($F_{religion}$):** Nếu $A$ hoặc $B$ yêu cầu bắt buộc đối phương phải có cùng tôn giáo ($LocReligion = 1$), nhưng tôn giáo của hai người khác nhau.
   $$F_{religion}(A, B) = \begin{cases} 
     0 & \text{nếu } Val_{A, religion} \neq Val_{B, religion} \\ 
     1 & \text{nếu ngược lại} 
   \end{cases}$$

3. **Ràng buộc về Văn hóa vùng miền ($F_{culture}$):** Nếu $A$ hoặc $B$ yêu cầu bắt buộc đối phương phải cùng vùng miền ($LocCulture = 1$), nhưng vùng miền của hai người khác nhau.
   $$F_{culture}(A, B) = \begin{cases} 
     0 & \text{nếu } Val_{A, culture} \neq Val_{B, culture} \\ 
     1 & \text{nếu ngược lại} 
   \end{cases}$$

**Tích tích lũy bộ lọc cứng:**
$$F(A, B) = F_{gender}(A, B) \times F_{religion}(A, B) \times F_{culture}(A, B)$$
> Nếu $F(A, B) = 0 \implies P_{match} = 0\%$ (Ứng viên bị loại bỏ ngay lập tức).

---

#### Giai đoạn 2: Tính điểm tương đồng trọng số (Soft Matching)

Nếu vượt qua Giai đoạn 1 ($F(A, B) = 1$), hệ thống tiến hành tính toán điểm dựa trên các công thức sau:

**Bước 1: Tính trọng số lai cho từng tiêu chí ($W_{hybrid, i}$)**
$$W_{hybrid, i} = W_{base, i} \times \big( (1 - U_i) + (U_i \times P_{boost, i}) \big)$$

*   *Ý nghĩa:* 
    *   Nếu không ưu tiên ($U_i = 0$), $W_{hybrid, i} = W_{base, i}$.
    *   Nếu có ưu tiên ($U_i = 1$), $W_{hybrid, i} = W_{base, i} \times P_{boost, i}$.

**Bước 2: Tính hệ số khớp thói quen ($M_i$)**
*   **Đối với thang đo tuyến tính 1-5 (`scale5`):**
    $$M_i = \begin{cases} 
      1.0 & \text{nếu } Val_{A, i} = Val_{B, i} \\ 
      0.5 & \text{nếu } |Val_{A, i} - Val_{B, i}| = 1 \\ 
      0.0 & \text{nếu } |Val_{A, i} - Val_{B, i}| > 1 
    \end{cases}$$
    > [!IMPORTANT]
    > **Khi chênh lệch 2 đơn vị hoặc hơn:** 
    > Nếu khoảng cách $|Val_{A, i} - Val_{B, i}| \ge 2$, hệ số khớp $M_i$ bằng **`0.0`**. Điều này phản ánh thực tế rằng sự lệch thói quen lớn (ví dụ một người rất ngăn nắp [4 hoặc 5] ở cùng một người bừa bộn [1 hoặc 2]) sẽ dễ dẫn tới xung đột và không thể bù đắp tương đồng lối sống, do đó tiêu chí này nhận 0 điểm.

*   **Đối với các tiêu chí dạng phân loại lựa chọn/đúng sai (`boolean`/`select`):**
    $$M_i = \begin{cases} 
      1.0 & \text{nếu } Val_{A, i} = Val_{B, i} \\ 
      0.0 & \text{nếu } Val_{A, i} \neq Val_{B, i} 
    \end{cases}$$

**Bước 3: Tính toán điểm tương đồng chuẩn hóa ($P_{match}$)**
Hợp nhất toàn bộ các điểm số thành phần bằng công thức tổng chuẩn hóa:
$$P_{match} = \left( \frac{\sum_{i \in C_{answered}} M_i \times W_{hybrid, i}}{\sum_{i \in C_{answered}} W_{hybrid, i}} \right) \times 100\%$$

---

### 1.4. Cách Đọc Công Thức Toán Học Trong Luận Văn (Đồng bộ với Chương 1)

1. **Công thức tính trọng số lai $W_{hybrid, i}$:**
   * *Đọc là:* "Trọng số lai $W_{hybrid}$ của tiêu chí $i$ được tính bằng tích của trọng số nền $W_{base, i}$ và tổng của hai thành phần: thành phần thứ nhất là hiệu của 1 và biến chỉ thị ưu tiên cá nhân $U_i$; thành phần thứ hai là tích của biến chỉ thị ưu tiên $U_i$ và hệ số nhân ưu tiên $P_{boost, i}$."
2. **Công thức tính hệ số khớp thói quen $M_i$ đối với thang đo 1-5:**
   * *Đọc là:* "Hệ số khớp thói quen $M_i$ đối với tiêu chí $i$ sẽ bằng $1.0$ nếu giá trị câu trả lời của người dùng $A$ và $B$ bằng nhau ($Val_{A, i} = Val_{B, i}$). Hệ số này sẽ nhận giá trị $0.5$ nếu hiệu số tuyệt đối giữa hai câu trả lời bằng $1$ (chênh lệch đúng 1 bậc). Trong tất cả các trường hợp khác, khi độ chênh lệch tuyệt đối từ $2$ đơn vị trở lên, hệ số khớp sẽ nhận giá trị bằng $0.0$."
3. **Ký hiệu tổng $\sum$ (Sigma) trong công thức $P_{match}$:**
   * *Đọc là:* "Tỷ lệ tương đồng $P_{match}$ được xác định bằng tỉ số giữa tổng các tích của hệ số khớp $M_i$ và trọng số lai $W_{hybrid, i}$ chia cho tổng các trọng số lai $W_{hybrid, i}$ của tất cả các tiêu chí $i$ thuộc tập hợp các câu hỏi đã trả lời $C_{answered}$."

---

### 1.5. Ví Dụ Tính Toán Thực Tế Từng Bước (Đồng bộ hóa ký hiệu)

Giả sử chúng ta có dữ liệu của 2 người dùng $A$ và $B$ như sau:

#### Bước chuẩn bị dữ liệu:
* **Thông tin cơ bản:**
  * Giới tính: $G_A = \text{"nam"}$, $G_B = \text{"nam"}$ $\implies F_{gender}(A, B) = 1$.
  * Tôn giáo: $Val_{A, religion} = \text{"khong"}$, $Val_{B, religion} = \text{"khong"}$, không yêu cầu lọc tôn giáo ($LocReligion = 0$) $\implies F_{religion}(A, B) = 1$.
  * Văn hóa: $Val_{A, culture} = \text{"mien\_bac"}$, $Val_{B, culture} = \text{"mien\_bac"}$, không yêu cầu lọc văn hóa ($LocCulture = 0$) $\implies F_{culture}(A, B) = 1$.
  * Tích bộ lọc cứng: $F(A, B) = 1 \times 1 \times 1 = 1$ $\implies$ **Đủ điều kiện làm tính điểm tương đồng**.

* **Cấu hình Trọng số Hệ thống:**
  1. `gio_giac`: $W_{base, 1} = 2.0$, $P_{boost, 1} = 2.0$ (loại `select`)
  2. `do_sach_se`: $W_{base, 2} = 1.5$, $P_{boost, 2} = 1.5$ (loại `scale5`)
  3. `hut_thuoc`: $W_{base, 3} = 3.0$, $P_{boost, 3} = 2.5$ (loại `select`)
  4. `nuoi_thu_cung`: $W_{base, 4} = 1.0$, $P_{boost, 4} = 1.2$ (loại `select`)

* **Khảo sát lối sống của $A$ và $B$:**
  * Danh sách ưu tiên của $A$: Chứa `gio_giac` và `hut_thuoc` $\implies$ $U_1 = 1$, $U_2 = 0$, $U_3 = 1$, $U_4 = 0$.
  * Câu trả lời cụ thể:
    | Tiêu chí ($i$) | Câu trả lời $Val_{A, i}$ | Câu trả lời $Val_{B, i}$ | Ưu tiên ($U_i$) | Loại input |
    | :--- | :---: | :---: | :---: | :--- |
    | 1. `gio_giac` | `"chim_som"` | `"chim_som"` | **1** | `select` |
    | 2. `do_sach_se` | `4` | `3` | **0** | `scale5` |
    | 3. `hut_thuoc` | `"0"` (Không hút) | `"1"` (Có hút) | **1** | `select` |
    | 4. `nuoi_thu_cung` | `"1"` (Yêu thú cưng) | `"1"` (Yêu thú cưng) | **0** | `select` |

---

#### Bước tính toán chi tiết:

##### 1. Tính Trọng số Lai ($W_{hybrid, i}$) cho từng tiêu chí của $A$:
* Tiêu chí 1 (`gio_giac` - Có ưu tiên $U_1 = 1$):
  $$W_{hybrid, 1} = 2.0 \times (0 + 1 \times 2.0) = 4.0$$
* Tiêu chí 2 (`do_sach_se` - Không ưu tiên $U_2 = 0$):
  $$W_{hybrid, 2} = 1.5 \times (1 + 0 \times 1.5) = 1.5$$
* Tiêu chí 3 (`hut_thuoc` - Có ưu tiên $U_3 = 1$):
  $$W_{hybrid, 3} = 3.0 \times (0 + 1 \times 2.5) = 7.5$$
* Tiêu chí 4 (`nuoi_thu_cung` - Không ưu tiên $U_4 = 0$):
  $$W_{hybrid, 4} = 1.0 \times (1 + 0 \times 1.2) = 1.0$$

$$\implies \sum W_{hybrid, i} = 4.0 + 1.5 + 7.5 + 1.0 = 14.0 \quad (\text{Mẫu số})$$

##### 2. Tính Hệ số khớp thói quen ($M_i$) giữa $A$ và $B$:
* Tiêu chí 1 (`gio_giac`): Khớp hoàn toàn ($Val_{A, 1} = Val_{B, 1} = \text{"chim\_som"}$)
  $$M_1 = 1.0$$
* Tiêu chí 2 (`do_sach_se`): Lệch 1 đơn vị ($|4 - 3| = 1$, loại `scale5`)
  $$M_2 = 0.5$$
* Tiêu chí 3 (`hut_thuoc`): Không khớp ($Val_{A, 3} = \text{"0"}$, $Val_{B, 3} = \text{"1"}$, loại `select`)
  $$M_3 = 0.0$$
* Tiêu chí 4 (`nuoi_thu_cung`): Khớp hoàn toàn ($Val_{A, 4} = Val_{B, 4} = \text{"1"}$)
  $$M_4 = 1.0$$

##### 3. Tính điểm Phần trăm tương đồng cuối cùng ($P_{match}$):
$$\sum M_i \times W_{hybrid, i} = (1.0 \times 4.0) + (0.5 \times 1.5) + (0.0 \times 7.5) + (1.0 \times 1.0)$$
$$\sum M_i \times W_{hybrid, i} = 4.0 + 0.75 + 0.0 + 1.0 = 5.75 \quad (\text{Tử số})$$

$$P_{match} = \left( \frac{5.75}{14.0} \right) \times 100\% \approx 41.07\%$$

**Kết luận:** Độ tương thích lối sống giữa $A$ và $B$ đạt **$41.07\%$**.

---

## 2. THUẬT TOÁN GỢI Ý PHÒNG TRỌ KHÔNG GIAN ĐA TIÊU CHÍ

### 2.1. Mục tiêu
Hệ thống đề xuất các phòng trọ thỏa mãn 2 điều kiện:
1. **Lọc không gian địa lý:** Nằm trong bán kính $d$ (ví dụ: $2000$ mét) xung quanh tọa độ của một địa điểm trọng tâm (trường học, cơ quan) do người dùng lựa chọn.
2. **Sắp xếp theo thứ hạng tin cậy:** Ưu tiên hiển thị các phòng có độ xác minh pháp lý cao hơn và thời gian cập nhật mới nhất.

---

### 2.2. Các Ký Hiệu Toán Học Và Giải Thích

| Ký hiệu | Ý nghĩa | Ví dụ |
| :--- | :--- | :--- |
| $P_{center}$ | Điểm trung tâm do người dùng chọn (Vĩ độ $\varphi_c$, Kinh độ $\lambda_c$) | Đại học Lâm Nghiệp ($21.0064^\circ \text{N}, 105.5786^\circ \text{E}$) |
| $P_{room}$ | Vị trí thực tế của phòng trọ (Vĩ độ $\varphi_r$, Kinh độ $\lambda_r$) | Tọa độ điểm lưu trữ dạng hình học hình học (Point) |
| $d(P_1, P_2)$ | Khoảng cách thực tế giữa hai điểm trên mặt cầu (mét) | Tính bằng công thức Haversine hoặc PostGIS |
| $d_{limit}$ | Bán kính quét tối đa cho phép (mét) | Mặc định trong code là $2000\text{ m}$ (2 km) |
| $V_{level}$ | Mức độ xác thực thông tin phòng trọ | $0$: Chưa duyệt, $1$: Xác thực cơ bản, $2$: Đã xác thực giấy tờ |
| $ID_{room}$ | Số thứ tự định danh duy nhất của phòng trọ trong CSDL | Dùng để xác định tính mới của bài đăng (ID càng lớn phòng càng mới) |
| $S_{sort}$ | Chỉ số ưu tiên dùng để xếp hạng hiển thị phòng trọ | Chỉ số này càng cao, phòng trọ hiển thị càng sớm |

---

### 2.3. Mô Hình Toán Học Của Thuật Toán

#### Giai đoạn 1: Lọc khoảng cách không gian (Spatial Filtering)
Hệ thống sử dụng cơ sở dữ liệu Postgres tích hợp **PostGIS** để tính toán khoảng cách đường cong mặt cầu giữa hai tọa độ địa lý.
Hàm được sử dụng trong code là:
`ST_DistanceSphere(vi_tri, ST_SetSRID(ST_MakePoint(lng, lat), 4326)) <= ban_kinh`

Về mặt toán học, hàm này giải công thức **Haversine**:
$$d(P_{center}, P_{room}) = 2R \cdot \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta \varphi}{2}\right) + \cos(\varphi_c)\cos(\varphi_r)\sin^2\left(\frac{\Delta \lambda}{2}\right)}\right)$$

Trong đó:
* $R \approx 6,371,000 \text{ mét}$ (Bán kính trung bình của Trái Đất).
* $\Delta \varphi = \varphi_r - \varphi_c$ (Hiệu số vĩ độ, quy đổi sang Radian).
* $\Delta \lambda = \lambda_r - \lambda_c$ (Hiệu số kinh độ, quy đổi sang Radian).

**Điều kiện lọc:**
$$\text{Giữ lại phòng trọ nếu: } d(P_{center}, P_{room}) \le d_{limit}$$

#### Giai đoạn 2: Sắp xếp tối ưu thứ tự hiển thị (Ranking & Sorting)
Sau khi lọc các phòng trọ nằm trong bán kính cho phép, hệ thống tiến hành tính toán chỉ số ưu tiên $S_{sort}$ để sắp xếp danh sách kết quả trả về cho người dùng:
$$S_{sort} = V_{level} \times 1000 + ID_{room}$$

**Quy tắc hiển thị:**
* Sắp xếp danh sách giảm dần theo $S_{sort}$ (phòng có chỉ số cao hơn xếp trước).
* **Ý nghĩa toán học:** Hệ số nhân $1000$ đóng vai trò tạo ra các phân lớp (bucket) ưu tiên tuyệt đối dựa trên mức độ xác thực $V_{level}$. 
  * Ví dụ, tất cả các phòng có $V_{level} = 2$ (Đã xác thực cao cấp) sẽ có điểm bắt đầu từ $2000 + ID_{room}$.
  * Tất cả các phòng có $V_{level} = 1$ (Xác thực cơ bản) sẽ có điểm bắt đầu từ $1000 + ID_{room}$.
  * Điều này đảm bảo: **Một phòng đã xác thực cao cấp luôn xếp trên một phòng chỉ xác thực cơ bản, bất kể thời gian đăng (ID) của phòng kia mới thế nào**.
  * Trong cùng một phân lớp xác thực ($V_{level}$ bằng nhau), phòng nào có $ID_{room}$ lớn hơn (tức là được tạo mới hơn) sẽ được xếp lên trên.

---

### 2.4. Hướng Dẫn Đọc Công Thức Toán Học Trong Luận Văn

1. **Công thức khoảng cách Haversine:**
   * *Đọc là:* "Khoảng cách $d$ giữa điểm trung tâm và phòng trọ bằng $2$ lần bán kính Trái Đất $R$ nhân với hàm arcsin (hàm ngược của sin) của căn bậc hai của tổng hai thành phần: thành phần thứ nhất là bình phương của sin của hiệu số vĩ độ chia hai; thành phần thứ hai là tích của cosin vĩ độ điểm trung tâm, cosin vĩ độ phòng trọ, và bình phương của sin của hiệu số kinh độ chia hai."
2. **Công thức chỉ số sắp xếp $S_{sort}$:**
   * *Đọc là:* "Chỉ số sắp xếp $S_{sort}$ bằng mức độ xác thực $V_{level}$ nhân với hệ số phân tách $1000$, sau đó cộng với mã số định danh $ID_{room}$ của phòng trọ. Việc sử dụng hệ số nhân $1000$ giúp thiết lập thứ tự ưu tiên tuyệt đối cho các bài đăng đã được xác minh trước khi xét tới yếu tố thời gian."

---

## 3. TỔNG KẾT VÀ KHUYẾN NGHỊ TRÌNH BÀY TRONG KỶ YẾU

Khi viết báo cáo khóa luận, bạn nên trình bày theo cấu trúc chuẩn khoa học:
1. **Đặt vấn đề:** Nêu rõ sự cần thiết của thuật toán (Tạo sao cần so khớp lối sống? Tại sao phải dùng GIS thay vì chỉ lọc văn bản thông thường?).
2. **Thiết lập mô hình:** Đưa các hệ phương trình và công thức ở các mục 1.3 và 2.3 vào báo cáo. Bạn có thể chép trực tiếp mã nguồn LaTeX từ tài liệu này vào các phần mềm soạn thảo như Overleaf/MS Word.
3. **Phân tích độ phức tạp thuật toán:**
   * **Thuật toán tìm bạn:** Độ phức tạp thời gian là $O(N \times M)$ với $N$ là số lượng ứng viên cùng giới tính trong hệ thống và $M$ là số lượng tiêu chí lối sống (thường $M \le 10$ là hằng số nên độ phức tạp thực tế đạt mức tuyến tính $O(N)$). Đây là thuật toán tối ưu, chạy trực tiếp trên máy chủ web mà không gây trễ hệ thống.
   * **Thuật toán tìm phòng:** Nhờ có Spatial Index (chỉ mục không gian GIST) trên PostgreSQL đối với cột `vi_tri`, việc lọc phòng trong bán kính đạt tốc độ cực nhanh $O(\log N)$ thay vì quét toàn bộ bảng $O(N)$.
